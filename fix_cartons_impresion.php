<?php
$local = new PDO('mysql:host=inveb-mysql-compose;dbname=envases_ot', 'root', 'root');
$local->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== Actualizando cartons locales con impresion_id ===\n";

// Actualizar todos los cartons activos locales para que tengan impresion_id
// Valor típico: '2,5,6,7' (Flexografía, Offset, etc.) y planta_id '1,2,3'
$sql = "UPDATE cartons SET impresion_id = '1,2,3,4,5,6,7', planta_id = '1,2,3' WHERE active = 1 AND (impresion_id IS NULL OR impresion_id = '')";
$result = $local->exec($sql);
echo "Cartons actualizados: $result\n";

echo "\n=== Verificación ===\n";
$r = $local->query("SELECT id, codigo, color_tapa_exterior, impresion_id, planta_id FROM cartons WHERE active = 1");
while($row = $r->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: {$row['id']}, Codigo: {$row['codigo']}, Color: {$row['color_tapa_exterior']}, Impresion: {$row['impresion_id']}, Planta: {$row['planta_id']}\n";
}
