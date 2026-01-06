<?php
$pdo = new PDO('mysql:host=inveb-mysql-compose;dbname=envases_ot', 'root', 'root');
echo "=== CÓDIGOS DE MATERIAL DISPONIBLES ===\n";
$r = $pdo->query('SELECT id, codigo, descripcion FROM materials WHERE codigo IS NOT NULL AND codigo != "" LIMIT 15');
while($row = $r->fetch(PDO::FETCH_ASSOC)) {
    $desc = substr($row['descripcion'] ?? '', 0, 40);
    echo "ID {$row['id']}: {$row['codigo']} - $desc\n";
}

echo "\n=== CÓDIGOS DE PRODUCTO EN OTs ===\n";
$r = $pdo->query('SELECT id, codigo_producto, material_code FROM work_orders WHERE (codigo_producto IS NOT NULL AND codigo_producto != "") OR (material_code IS NOT NULL AND material_code != "") LIMIT 10');
while($row = $r->fetch(PDO::FETCH_ASSOC)) {
    echo "OT {$row['id']}: codigo={$row['codigo_producto']}, material_code={$row['material_code']}\n";
}
