<?php
$qas = new PDO("mysql:host=envases-ot.inveb.cl;dbname=envases_ot", "tandina", "1a35a2f5a454526a7fb54f98da4117f0");
$local = new PDO("mysql:host=inveb-mysql-compose;dbname=envases_ot", "root", "root");

echo "=== SINCRONIZANDO relacion_filtro_ingresos_principales ===\n\n";

// Obtener estructura de la tabla
$cols = $qas->query("SHOW COLUMNS FROM relacion_filtro_ingresos_principales")->fetchAll(PDO::FETCH_COLUMN);
echo "Columnas: " . implode(", ", $cols) . "\n\n";

// Limpiar tabla local
$local->exec("DELETE FROM relacion_filtro_ingresos_principales");
echo "Tabla local limpiada\n";

// Obtener todos los registros de QAS
$rows = $qas->query("SELECT * FROM relacion_filtro_ingresos_principales")->fetchAll(PDO::FETCH_ASSOC);
echo "Registros a sincronizar: " . count($rows) . "\n\n";

$inserted = 0;
foreach($rows as $row) {
    $columns = array_keys($row);
    $placeholders = array_fill(0, count($columns), '?');

    $sql = "INSERT INTO relacion_filtro_ingresos_principales (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $placeholders) . ")";

    try {
        $stmt = $local->prepare($sql);
        $stmt->execute(array_values($row));
        $inserted++;
    } catch(Exception $e) {
        echo "Error insertando registro: " . $e->getMessage() . "\n";
        print_r($row);
    }
}

echo "\nRegistros insertados: $inserted\n";

// Verificar
$count = $local->query("SELECT COUNT(*) FROM relacion_filtro_ingresos_principales")->fetchColumn();
echo "\nVerificación - Total en LOCAL: $count registros\n";

// Mostrar referencias únicas
echo "\n=== Referencias únicas sincronizadas ===\n";
$refs = $local->query("SELECT DISTINCT referencia, COUNT(*) as cnt FROM relacion_filtro_ingresos_principales GROUP BY referencia")->fetchAll(PDO::FETCH_ASSOC);
foreach($refs as $ref) {
    echo "- {$ref['referencia']}: {$ref['cnt']} registros\n";
}
