<?php
$qas = new PDO("mysql:host=envases-ot.inveb.cl;dbname=envases_ot", "tandina", "1a35a2f5a454526a7fb54f98da4117f0");
$local = new PDO("mysql:host=inveb-mysql-compose;dbname=envases_ot", "root", "root");

echo "=== Tablas con 'refer' en QAS ===\n";
$r = $qas->query("SHOW TABLES LIKE '%refer%'");
while($row = $r->fetch(PDO::FETCH_NUM)) {
    $t = $row[0];
    $c = $qas->query("SELECT COUNT(*) FROM $t")->fetchColumn();
    echo "$t: $c registros\n";
}

echo "\n=== Verificar reference_types ===\n";
try {
    $qasC = $qas->query("SELECT COUNT(*) FROM reference_types")->fetchColumn();
    echo "QAS reference_types: $qasC\n";
    $sample = $qas->query("SELECT * FROM reference_types LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    print_r($sample);
} catch(Exception $e) {
    echo "QAS: No existe reference_types\n";
}

echo "\n=== Verificar en LOCAL ===\n";
try {
    $localC = $local->query("SELECT COUNT(*) FROM reference_types")->fetchColumn();
    echo "LOCAL reference_types: $localC\n";
} catch(Exception $e) {
    echo "LOCAL: No existe reference_types\n";
}

echo "\n=== Tablas con 'carton' ===\n";
$r = $qas->query("SHOW TABLES LIKE '%carton%'");
while($row = $r->fetch(PDO::FETCH_NUM)) {
    $t = $row[0];
    $c = $qas->query("SELECT COUNT(*) FROM $t")->fetchColumn();
    echo "QAS $t: $c registros\n";
}

try {
    $localC = $local->query("SELECT COUNT(*) FROM cartons")->fetchColumn();
    echo "LOCAL cartons: $localC\n";
} catch(Exception $e) {}
