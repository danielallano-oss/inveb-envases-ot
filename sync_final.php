<?php
ini_set('memory_limit', '512M');

$qas = new PDO("mysql:host=envases-ot.inveb.cl;dbname=envases_ot", "tandina", "1a35a2f5a454526a7fb54f98da4117f0");
$local = new PDO("mysql:host=inveb-mysql-compose;dbname=envases_ot", "root", "root");
$qas->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$local->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Buscar tabla para color cartón
echo "=== Buscando tabla COLOR CARTÓN ===\n";
$r = $qas->query("SHOW TABLES LIKE '%color%'");
while($row = $r->fetch(PDO::FETCH_NUM)) {
    $t = $row[0];
    $c = $qas->query("SELECT COUNT(*) FROM $t")->fetchColumn();
    echo "$t: $c registros\n";
}

echo "\n=== Buscando tabla TIPO PRODUCTO ===\n";
$r = $qas->query("SHOW TABLES LIKE '%product%'");
while($row = $r->fetch(PDO::FETCH_NUM)) {
    $t = $row[0];
    $c = $qas->query("SELECT COUNT(*) FROM $t")->fetchColumn();
    echo "$t: $c registros\n";
}

// Sincronizar tablas faltantes
$tables = ['impresion', 'product_type_developing', 'tipos_cintas'];

foreach($tables as $table) {
    echo "\n=== Sincronizando $table ===\n";

    try {
        $qasCount = $qas->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        echo "QAS: $qasCount\n";
    } catch(Exception $e) {
        echo "QAS: No existe\n";
        continue;
    }

    // Crear si no existe
    try {
        $localCount = $local->query("SELECT COUNT(*) FROM $table")->fetchColumn();
    } catch(Exception $e) {
        $create = $qas->query("SHOW CREATE TABLE $table")->fetch(PDO::FETCH_ASSOC);
        $local->exec($create['Create Table']);
        echo "Tabla creada\n";
        $localCount = 0;
    }

    if($localCount == 0 && $qasCount > 0) {
        $rows = $qas->query("SELECT * FROM $table")->fetchAll(PDO::FETCH_ASSOC);
        foreach($rows as $row) {
            foreach($row as $k => $v) {
                if($v === '0000-00-00 00:00:00') $row[$k] = null;
            }
            $cols = array_keys($row);
            $vals = array_map(fn($v) => $v === null ? 'NULL' : $local->quote($v), array_values($row));
            $sql = "INSERT INTO $table (`" . implode("`,`", $cols) . "`) VALUES (" . implode(",", $vals) . ")";
            $sql = str_replace("'NULL'", "NULL", $sql);
            try { $local->exec($sql); } catch(Exception $e) {}
        }
        echo "Datos copiados\n";
    }
}

// Sincronizar materials (limitado a los usados en OTs + 500 adicionales)
echo "\n=== Sincronizando materials (parcial) ===\n";
$localCount = $local->query("SELECT COUNT(*) FROM materials")->fetchColumn();
echo "LOCAL actual: $localCount\n";

if($localCount == 0) {
    // Crear tabla
    try {
        $local->query("SELECT 1 FROM materials LIMIT 1");
    } catch(Exception $e) {
        $create = $qas->query("SHOW CREATE TABLE materials")->fetch(PDO::FETCH_ASSOC);
        $local->exec($create['Create Table']);
        echo "Tabla materials creada\n";
    }

    // Copiar primeros 2000 materiales
    echo "Copiando 2000 materiales...\n";
    $rows = $qas->query("SELECT * FROM materials LIMIT 2000")->fetchAll(PDO::FETCH_ASSOC);
    $inserted = 0;
    foreach($rows as $row) {
        foreach($row as $k => $v) {
            if($v === '0000-00-00 00:00:00') $row[$k] = null;
        }
        $cols = array_keys($row);
        $vals = array_map(fn($v) => $v === null ? 'NULL' : $local->quote($v), array_values($row));
        $sql = "INSERT INTO materials (`" . implode("`,`", $cols) . "`) VALUES (" . implode(",", $vals) . ")";
        $sql = str_replace("'NULL'", "NULL", $sql);
        try {
            $local->exec($sql);
            $inserted++;
        } catch(Exception $e) {}
    }
    echo "Insertados: $inserted\n";
}

echo "\n=== VERIFICACIÓN FINAL ===\n";
$check = ['impresion', 'materials', 'product_type_developing', 'tipos_cintas', 'colors', 'cartons'];
foreach($check as $t) {
    try {
        $c = $local->query("SELECT COUNT(*) FROM $t")->fetchColumn();
        echo "$t: $c registros\n";
    } catch(Exception $e) {
        echo "$t: NO EXISTE\n";
    }
}

echo "\n=== CÓDIGOS DE PRODUCTO EN OTs ===\n";
$r = $local->query("SELECT id, codigo_producto, material_code FROM work_orders WHERE (codigo_producto IS NOT NULL AND codigo_producto != '') OR (material_code IS NOT NULL AND material_code != '') LIMIT 5");
while($row = $r->fetch(PDO::FETCH_ASSOC)) {
    echo "OT {$row['id']}: codigo={$row['codigo_producto']}, material_code={$row['material_code']}\n";
}
