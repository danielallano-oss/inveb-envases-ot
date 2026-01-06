<?php
$qas = new PDO("mysql:host=envases-ot.inveb.cl;dbname=envases_ot", "tandina", "1a35a2f5a454526a7fb54f98da4117f0");
$local = new PDO("mysql:host=inveb-mysql-compose;dbname=envases_ot", "root", "root");
$qas->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$local->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== Sincronizando carton_esquineros ===\n";

// Verificar si la tabla existe
try {
    $exists = $local->query("SHOW TABLES LIKE 'carton_esquineros'")->fetch();
    if(!$exists) {
        echo "Creando tabla...\n";
        $create = $qas->query("SHOW CREATE TABLE carton_esquineros")->fetch(PDO::FETCH_ASSOC);
        $local->exec($create['Create Table']);
    }
} catch(Exception $e) {
    echo "Error verificando tabla: " . $e->getMessage() . "\n";
}

$localCount = $local->query("SELECT COUNT(*) FROM carton_esquineros")->fetchColumn();
echo "LOCAL actual: $localCount\n";

$qasCount = $qas->query("SELECT COUNT(*) FROM carton_esquineros")->fetchColumn();
echo "QAS: $qasCount\n";

if($localCount == 0) {
    $rows = $qas->query("SELECT * FROM carton_esquineros")->fetchAll(PDO::FETCH_ASSOC);
    echo "Insertando " . count($rows) . " registros...\n";
    
    foreach($rows as $row) {
        foreach($row as $k => $v) {
            if($v === '0000-00-00 00:00:00' || $v === '0000-00-00') $row[$k] = null;
        }
        $cols = array_keys($row);
        $vals = array_map(fn($v) => $v === null ? 'NULL' : $local->quote($v), array_values($row));
        $sql = "INSERT INTO carton_esquineros (`" . implode("`,`", $cols) . "`) VALUES (" . implode(",", $vals) . ")";
        $sql = str_replace("'NULL'", "NULL", $sql);
        try {
            $local->exec($sql);
            echo ".";
        } catch(Exception $e) {
            echo "X";
        }
    }
    echo "\n";
}

$finalCount = $local->query("SELECT COUNT(*) FROM carton_esquineros")->fetchColumn();
echo "LOCAL final: $finalCount registros\n";
