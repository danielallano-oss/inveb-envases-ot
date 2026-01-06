<?php
$qas = new PDO("mysql:host=envases-ot.inveb.cl;dbname=envases_ot", "tandina", "1a35a2f5a454526a7fb54f98da4117f0");
$local = new PDO("mysql:host=inveb-mysql-compose;dbname=envases_ot", "root", "root");
$qas->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$local->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Sincronizar cartons faltantes
echo "=== Sincronizando cartons adicionales ===\n";
$localIds = $local->query("SELECT id FROM cartons")->fetchAll(PDO::FETCH_COLUMN);
$qasRows = $qas->query("SELECT * FROM cartons")->fetchAll(PDO::FETCH_ASSOC);
$inserted = 0;
foreach($qasRows as $row) {
    if(!in_array($row['id'], $localIds)) {
        foreach($row as $k => $v) {
            if($v === '0000-00-00 00:00:00') $row[$k] = null;
        }
        $cols = array_keys($row);
        $vals = array_map(fn($v) => $v === null ? 'NULL' : $local->quote($v), array_values($row));
        $sql = "INSERT INTO cartons (" . implode(",", $cols) . ") VALUES (" . implode(",", $vals) . ")";
        $sql = str_replace("'NULL'", "NULL", $sql);
        try { $local->exec($sql); $inserted++; } catch(Exception $e) {}
    }
}
echo "Cartons insertados: $inserted\n";

// Sincronizar colors faltantes
echo "\n=== Sincronizando colors adicionales ===\n";
$localCount = $local->query("SELECT COUNT(*) FROM colors")->fetchColumn();
$qasCount = $qas->query("SELECT COUNT(*) FROM colors")->fetchColumn();
echo "LOCAL: $localCount, QAS: $qasCount\n";

if($localCount < $qasCount) {
    $localIds = $local->query("SELECT id FROM colors")->fetchAll(PDO::FETCH_COLUMN);
    $qasRows = $qas->query("SELECT * FROM colors LIMIT 500")->fetchAll(PDO::FETCH_ASSOC);
    $inserted = 0;
    foreach($qasRows as $row) {
        if(!in_array($row['id'], $localIds)) {
            foreach($row as $k => $v) {
                if($v === '0000-00-00 00:00:00') $row[$k] = null;
            }
            $cols = array_keys($row);
            $vals = array_map(fn($v) => $v === null ? 'NULL' : $local->quote($v), array_values($row));
            $sql = "INSERT INTO colors (" . implode(",", $cols) . ") VALUES (" . implode(",", $vals) . ")";
            $sql = str_replace("'NULL'", "NULL", $sql);
            try { $local->exec($sql); $inserted++; } catch(Exception $e) {}
        }
    }
    echo "Colors insertados: $inserted\n";
}

// Sincronizar carton_esquineros si no existe
echo "\n=== Verificando carton_esquineros ===\n";
try {
    $c = $local->query("SELECT COUNT(*) FROM carton_esquineros")->fetchColumn();
    echo "LOCAL carton_esquineros: $c\n";
} catch(Exception $e) {
    echo "Creando tabla carton_esquineros...\n";
    $create = $qas->query("SHOW CREATE TABLE carton_esquineros")->fetch(PDO::FETCH_ASSOC);
    $local->exec($create['Create Table']);
    
    $rows = $qas->query("SELECT * FROM carton_esquineros")->fetchAll(PDO::FETCH_ASSOC);
    $inserted = 0;
    foreach($rows as $row) {
        foreach($row as $k => $v) {
            if($v === '0000-00-00 00:00:00') $row[$k] = null;
        }
        $cols = array_keys($row);
        $vals = array_map(fn($v) => $v === null ? 'NULL' : $local->quote($v), array_values($row));
        $sql = "INSERT INTO carton_esquineros (" . implode(",", $cols) . ") VALUES (" . implode(",", $vals) . ")";
        $sql = str_replace("'NULL'", "NULL", $sql);
        try { $local->exec($sql); $inserted++; } catch(Exception $e) {}
    }
    echo "Insertados: $inserted\n";
}

echo "\n=== ESTADO FINAL ===\n";
$tables = ['reference_types', 'cartons', 'colors', 'impresion', 'materials', 'product_type_developing', 'tipos_cintas', 'carton_esquineros'];
foreach($tables as $t) {
    try {
        $c = $local->query("SELECT COUNT(*) FROM $t")->fetchColumn();
        echo "$t: $c registros\n";
    } catch(Exception $e) {
        echo "$t: NO EXISTE\n";
    }
}
