<?php
$pdo = new PDO('mysql:host=inveb-mysql-compose;dbname=envases_ot', 'root', 'root');

echo "=== Estructura de cartons ===\n";
$r = $pdo->query("DESCRIBE cartons");
while($row = $r->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}

echo "\n=== Cartons con active=1 ===\n";
$r = $pdo->query("SELECT COUNT(*) FROM cartons WHERE active = 1");
echo "Total activos: " . $r->fetchColumn() . "\n";

echo "\n=== Muestra de cartons (primeros 5 activos) ===\n";
$r = $pdo->query("SELECT id, codigo, color_tapa_exterior, impresion_id, planta_id, active FROM cartons WHERE active = 1 LIMIT 5");
while($row = $r->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: {$row['id']}, Codigo: {$row['codigo']}, Color: {$row['color_tapa_exterior']}, Impresion: {$row['impresion_id']}, Planta: {$row['planta_id']}\n";
}

echo "\n=== Cartons sin impresion_id o planta_id ===\n";
$r = $pdo->query("SELECT COUNT(*) FROM cartons WHERE active = 1 AND (impresion_id IS NULL OR impresion_id = '' OR planta_id IS NULL OR planta_id = '')");
echo "Sin impresion/planta: " . $r->fetchColumn() . "\n";

echo "\n=== Valores únicos de impresion_id en cartons ===\n";
$r = $pdo->query("SELECT DISTINCT impresion_id FROM cartons WHERE active = 1 AND impresion_id IS NOT NULL AND impresion_id != '' LIMIT 10");
while($row = $r->fetch(PDO::FETCH_COLUMN)) {
    echo "impresion_id: $row\n";
}

echo "\n=== Valores únicos de planta_id en cartons ===\n";
$r = $pdo->query("SELECT DISTINCT planta_id FROM cartons WHERE active = 1 AND planta_id IS NOT NULL AND planta_id != '' LIMIT 10");
while($row = $r->fetch(PDO::FETCH_COLUMN)) {
    echo "planta_id: $row\n";
}
