<?php
$qas = new PDO("mysql:host=envases-ot.inveb.cl;dbname=envases_ot", "tandina", "1a35a2f5a454526a7fb54f98da4117f0");
$local = new PDO("mysql:host=inveb-mysql-compose;dbname=envases_ot", "root", "root");

// Sincronizar plantas
echo "=== SINCRONIZANDO PLANTAS ===\n";
try {
    $rows = $qas->query("SELECT * FROM plantas")->fetchAll(PDO::FETCH_ASSOC);
    echo "QAS tiene: " . count($rows) . " plantas\n";

    if(count($rows) > 0) {
        $local->exec("DELETE FROM plantas");
        $inserted = 0;
        foreach($rows as $row) {
            $cols = array_keys($row);
            $placeholders = array_fill(0, count($cols), '?');
            $sql = "INSERT INTO plantas (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $placeholders) . ")";
            try {
                $stmt = $local->prepare($sql);
                $stmt->execute(array_values($row));
                $inserted++;
            } catch(Exception $e) {
                echo "Error: " . $e->getMessage() . "\n";
            }
        }
        echo "Insertadas: $inserted plantas\n";
    }
} catch(Exception $e) {
    echo "Error plantas: " . $e->getMessage() . "\n";
}

// Verificar carton_colors
echo "\n=== VERIFICANDO CARTON_COLORS ===\n";
try {
    $qasCount = $qas->query("SELECT COUNT(*) FROM carton_colors")->fetchColumn();
    echo "QAS tiene: $qasCount registros\n";

    // Verificar si existe localmente
    try {
        $localCount = $local->query("SELECT COUNT(*) FROM carton_colors")->fetchColumn();
        echo "LOCAL tiene: $localCount registros\n";
    } catch(Exception $e) {
        echo "LOCAL: Tabla no existe, creándola...\n";

        // Obtener estructura de QAS
        $createStmt = $qas->query("SHOW CREATE TABLE carton_colors")->fetch(PDO::FETCH_ASSOC);
        $createSQL = $createStmt['Create Table'];
        $local->exec($createSQL);
        echo "Tabla creada\n";
        $localCount = 0;
    }

    // Sincronizar si está vacía
    if($localCount == 0 && $qasCount > 0) {
        $rows = $qas->query("SELECT * FROM carton_colors")->fetchAll(PDO::FETCH_ASSOC);
        $inserted = 0;
        foreach($rows as $row) {
            $cols = array_keys($row);
            $placeholders = array_fill(0, count($cols), '?');
            $sql = "INSERT INTO carton_colors (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $placeholders) . ")";
            try {
                $stmt = $local->prepare($sql);
                $stmt->execute(array_values($row));
                $inserted++;
            } catch(Exception $e) {
                echo "Error: " . $e->getMessage() . "\n";
            }
        }
        echo "Insertados: $inserted registros\n";
    }
} catch(Exception $e) {
    echo "Error carton_colors: " . $e->getMessage() . "\n";
}

// Verificación final
echo "\n=== VERIFICACIÓN FINAL ===\n";
$tables = ['plantas', 'carton_colors', 'fsc', 'tipos_cintas', 'cartons'];
foreach($tables as $t) {
    try {
        $c = $local->query("SELECT COUNT(*) FROM $t")->fetchColumn();
        echo "$t: $c registros\n";
    } catch(Exception $e) {
        echo "$t: NO EXISTE\n";
    }
}
