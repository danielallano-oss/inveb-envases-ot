<?php
ini_set('memory_limit', '512M');
$qas = new PDO('mysql:host=envases-ot.inveb.cl;dbname=envases_ot', 'tandina', '1a35a2f5a454526a7fb54f98da4117f0');
$local = new PDO('mysql:host=inveb-mysql-compose;dbname=envases_ot', 'root', 'root');
$qas->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$local->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== Sincronizando cartons completos desde QAS ===\n";

// Obtener IDs locales
$localIds = $local->query("SELECT id FROM cartons")->fetchAll(PDO::FETCH_COLUMN);
echo "Cartons locales actuales: " . count($localIds) . "\n";

// Obtener cartons activos de QAS
$qasCartons = $qas->query("SELECT * FROM cartons WHERE active = 1")->fetchAll(PDO::FETCH_ASSOC);
echo "Cartons activos en QAS: " . count($qasCartons) . "\n";

$inserted = 0;
$updated = 0;

foreach($qasCartons as $carton) {
    foreach($carton as $k => $v) {
        if($v === '0000-00-00 00:00:00') $carton[$k] = null;
    }
    
    if(in_array($carton['id'], $localIds)) {
        // Actualizar
        $sql = "UPDATE cartons SET impresion_id = :imp, planta_id = :plt, color_tapa_exterior = :color WHERE id = :id";
        $stmt = $local->prepare($sql);
        $stmt->execute([
            ':imp' => $carton['impresion_id'],
            ':plt' => $carton['planta_id'],
            ':color' => $carton['color_tapa_exterior'],
            ':id' => $carton['id']
        ]);
        if($stmt->rowCount() > 0) $updated++;
    } else {
        // Insertar
        $cols = array_keys($carton);
        $vals = array_map(fn($v) => $v === null ? 'NULL' : $local->quote($v), array_values($carton));
        $sql = "INSERT INTO cartons (`" . implode("`,`", $cols) . "`) VALUES (" . implode(",", $vals) . ")";
        $sql = str_replace("'NULL'", "NULL", $sql);
        try {
            $local->exec($sql);
            $inserted++;
        } catch(Exception $e) {
            // Ignorar errores de duplicados
        }
    }
}

echo "Insertados: $inserted\n";
echo "Actualizados: $updated\n";

echo "\n=== Verificación final ===\n";
$r = $local->query("SELECT COUNT(*) FROM cartons WHERE active = 1");
echo "Cartons activos locales: " . $r->fetchColumn() . "\n";

$r = $local->query("SELECT COUNT(*) FROM cartons WHERE active = 1 AND impresion_id IS NOT NULL AND impresion_id != ''");
echo "Con impresion_id: " . $r->fetchColumn() . "\n";

echo "\n=== Muestra de cartons con impresion_id ===\n";
$r = $local->query("SELECT id, codigo, color_tapa_exterior, impresion_id, planta_id FROM cartons WHERE active = 1 AND impresion_id IS NOT NULL AND impresion_id != '' LIMIT 5");
while($row = $r->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: {$row['id']}, Codigo: {$row['codigo']}, Color: {$row['color_tapa_exterior']}, Impresion: {$row['impresion_id']}, Planta: {$row['planta_id']}\n";
}
