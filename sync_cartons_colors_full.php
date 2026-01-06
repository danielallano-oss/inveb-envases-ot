<?php
$qas = new PDO("mysql:host=envases-ot.inveb.cl;dbname=envases_ot", "tandina", "1a35a2f5a454526a7fb54f98da4117f0");
$local = new PDO("mysql:host=inveb-mysql-compose;dbname=envases_ot", "root", "root");

// Sincronizar colors que faltan
echo "=== SINCRONIZANDO COLORS ===\n";
$localIds = $local->query("SELECT id FROM colors")->fetchAll(PDO::FETCH_COLUMN);
$localIdsStr = implode(',', array_map('intval', $localIds));

$qasRows = $qas->query("SELECT * FROM colors WHERE id NOT IN ($localIdsStr)")->fetchAll(PDO::FETCH_ASSOC);
echo "Colors faltantes: " . count($qasRows) . "\n";

$inserted = 0;
foreach($qasRows as $row) {
    $cols = array_keys($row);
    $placeholders = array_fill(0, count($cols), '?');
    $sql = "INSERT INTO colors (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $placeholders) . ")";
    try {
        $stmt = $local->prepare($sql);
        $stmt->execute(array_values($row));
        $inserted++;
    } catch(Exception $e) {}
}
echo "Insertados: $inserted\n";

// Sincronizar cartons que faltan
echo "\n=== SINCRONIZANDO CARTONS ===\n";
$localIds = $local->query("SELECT id FROM cartons")->fetchAll(PDO::FETCH_COLUMN);
$localIdsStr = count($localIds) > 0 ? implode(',', array_map('intval', $localIds)) : '0';

$qasRows = $qas->query("SELECT * FROM cartons WHERE id NOT IN ($localIdsStr)")->fetchAll(PDO::FETCH_ASSOC);
echo "Cartons faltantes: " . count($qasRows) . "\n";

$inserted = 0;
foreach($qasRows as $row) {
    $cols = array_keys($row);
    $placeholders = array_fill(0, count($cols), '?');
    $sql = "INSERT INTO cartons (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $placeholders) . ")";
    try {
        $stmt = $local->prepare($sql);
        $stmt->execute(array_values($row));
        $inserted++;
    } catch(Exception $e) {
        // Puede haber columnas diferentes, intentar UPDATE
    }
}
echo "Insertados: $inserted\n";

// Verificación final
echo "\n=== VERIFICACIÓN FINAL ===\n";
echo "colors LOCAL: " . $local->query("SELECT COUNT(*) FROM colors")->fetchColumn() . "\n";
echo "cartons LOCAL: " . $local->query("SELECT COUNT(*) FROM cartons")->fetchColumn() . "\n";
echo "plantas LOCAL: " . $local->query("SELECT COUNT(*) FROM plantas")->fetchColumn() . "\n";
echo "fsc LOCAL: " . $local->query("SELECT COUNT(*) FROM fsc")->fetchColumn() . "\n";
echo "tipos_cintas LOCAL: " . $local->query("SELECT COUNT(*) FROM tipos_cintas")->fetchColumn() . "\n";
echo "relacion_filtro LOCAL: " . $local->query("SELECT COUNT(*) FROM relacion_filtro_ingresos_principales")->fetchColumn() . "\n";
