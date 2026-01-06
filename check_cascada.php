<?php
$qas = new PDO("mysql:host=envases-ot.inveb.cl;dbname=envases_ot", "tandina", "1a35a2f5a454526a7fb54f98da4117f0");
$local = new PDO("mysql:host=inveb-mysql-compose;dbname=envases_ot", "root", "root");

$tables = [
    'fsc' => 'FSC',
    'tipos_cintas' => 'CINTA',
    'recubrimiento_interno' => 'RECUBRIMIENTO INTERNO',
    'recubrimiento_externo' => 'RECUBRIMIENTO EXTERNO',
    'plantas' => 'PLANTAS',
    'carton_colors' => 'COLOR CARTÓN',
    'cartons' => 'CARTÓN'
];

echo "=== VERIFICACIÓN DE TABLAS PARA CASCADA ===\n\n";

foreach($tables as $table => $desc) {
    echo "--- $desc ($table) ---\n";

    try {
        $qasCount = $qas->query("SELECT COUNT(*) FROM $table")->fetchColumn();
    } catch(Exception $e) {
        $qasCount = "NO EXISTE";
    }

    try {
        $localCount = $local->query("SELECT COUNT(*) FROM $table")->fetchColumn();
    } catch(Exception $e) {
        $localCount = "NO EXISTE";
    }

    echo "QAS: $qasCount | LOCAL: $localCount\n";

    if(is_numeric($qasCount) && is_numeric($localCount) && $localCount == 0 && $qasCount > 0) {
        echo "  ⚠️ NECESITA SINCRONIZAR\n";
    }
    echo "\n";
}
