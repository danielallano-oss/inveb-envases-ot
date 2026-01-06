<?php
$qas = new PDO('mysql:host=envases-ot.inveb.cl;dbname=envases_ot', 'tandina', '1a35a2f5a454526a7fb54f98da4117f0');
$local = new PDO('mysql:host=inveb-mysql-compose;dbname=envases_ot', 'root', 'root');

echo "=== Tabla relacion_filtro_ingresos_principales ===\n";

// Verificar en QAS
try {
    $r = $qas->query("SELECT COUNT(*) FROM relacion_filtro_ingresos_principales");
    echo "QAS: " . $r->fetchColumn() . " registros\n";
} catch(Exception $e) {
    echo "QAS: No existe\n";
}

// Verificar en LOCAL
try {
    $r = $local->query("SELECT COUNT(*) FROM relacion_filtro_ingresos_principales");
    echo "LOCAL: " . $r->fetchColumn() . " registros\n";
} catch(Exception $e) {
    echo "LOCAL: No existe - " . $e->getMessage() . "\n";
}

echo "\n=== Muestra de datos QAS ===\n";
try {
    $r = $qas->query("SELECT * FROM relacion_filtro_ingresos_principales WHERE referencia = 'impresion_fsc' LIMIT 10");
    while($row = $r->fetch(PDO::FETCH_ASSOC)) {
        echo "filtro_1: {$row['filtro_1']}, filtro_2: {$row['filtro_2']}, planta_id: {$row['planta_id']}, referencia: {$row['referencia']}\n";
    }
} catch(Exception $e) {}
