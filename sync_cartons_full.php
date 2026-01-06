<?php
$qas = new PDO('mysql:host=envases-ot.inveb.cl;dbname=envases_ot', 'tandina', '1a35a2f5a454526a7fb54f98da4117f0');
$local = new PDO('mysql:host=inveb-mysql-compose;dbname=envases_ot', 'root', 'root');
$qas->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$local->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== Verificando cartons en QAS ===\n";
$r = $qas->query("SELECT COUNT(*) FROM cartons WHERE active = 1");
echo "QAS cartons activos: " . $r->fetchColumn() . "\n";

$r = $qas->query("SELECT COUNT(*) FROM cartons WHERE active = 1 AND impresion_id IS NOT NULL AND impresion_id != ''");
echo "QAS cartons con impresion_id: " . $r->fetchColumn() . "\n";

echo "\n=== Muestra de cartons QAS con impresion_id ===\n";
$r = $qas->query("SELECT id, codigo, color_tapa_exterior, impresion_id, planta_id FROM cartons WHERE active = 1 AND impresion_id IS NOT NULL AND impresion_id != '' LIMIT 10");
while($row = $r->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: {$row['id']}, Codigo: {$row['codigo']}, Color: {$row['color_tapa_exterior']}, Impresion: {$row['impresion_id']}, Planta: {$row['planta_id']}\n";
}

echo "\n=== Actualizando cartons locales desde QAS ===\n";
$qasCartons = $qas->query("SELECT * FROM cartons WHERE active = 1")->fetchAll(PDO::FETCH_ASSOC);
$updated = 0;

foreach($qasCartons as $carton) {
    // Actualizar el carton local con los datos de QAS
    $sql = "UPDATE cartons SET 
            impresion_id = :impresion_id,
            planta_id = :planta_id,
            color_tapa_exterior = :color
            WHERE id = :id";
    
    $stmt = $local->prepare($sql);
    $result = $stmt->execute([
        ':impresion_id' => $carton['impresion_id'],
        ':planta_id' => $carton['planta_id'],
        ':color' => $carton['color_tapa_exterior'],
        ':id' => $carton['id']
    ]);
    
    if($stmt->rowCount() > 0) {
        $updated++;
    }
}
echo "Cartons actualizados: $updated\n";

echo "\n=== Verificación final ===\n";
$r = $local->query("SELECT id, codigo, color_tapa_exterior, impresion_id, planta_id FROM cartons WHERE active = 1 AND impresion_id IS NOT NULL AND impresion_id != '' LIMIT 10");
echo "Cartons locales con impresion_id ahora:\n";
while($row = $r->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: {$row['id']}, Codigo: {$row['codigo']}, Color: {$row['color_tapa_exterior']}, Impresion: {$row['impresion_id']}, Planta: {$row['planta_id']}\n";
}
