<?php
$qas = new PDO("mysql:host=envases-ot.inveb.cl;dbname=envases_ot", "tandina", "1a35a2f5a454526a7fb54f98da4117f0");
$local = new PDO("mysql:host=inveb-mysql-compose;dbname=envases_ot", "root", "root");
$local->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Obtener IDs faltantes
$localIds = $local->query('SELECT id FROM cartons')->fetchAll(PDO::FETCH_COLUMN);
$qasIds = $qas->query('SELECT id FROM cartons')->fetchAll(PDO::FETCH_COLUMN);
$missing = array_diff($qasIds, $localIds);

echo "Cartons faltantes: " . count($missing) . "\n";

if(count($missing) > 0) {
    // Obtener columnas de la tabla local
    $localCols = $local->query('DESCRIBE cartons')->fetchAll(PDO::FETCH_COLUMN);

    $missingStr = implode(',', array_map('intval', $missing));
    $rows = $qas->query("SELECT * FROM cartons WHERE id IN ($missingStr)")->fetchAll(PDO::FETCH_ASSOC);

    $inserted = 0;
    $errors = 0;
    foreach($rows as $row) {
        // Solo usar columnas que existen en local
        $filteredRow = array_intersect_key($row, array_flip($localCols));
        $cols = array_keys($filteredRow);
        $placeholders = array_fill(0, count($cols), '?');

        $sql = "INSERT INTO cartons (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $placeholders) . ")";

        try {
            $stmt = $local->prepare($sql);
            $stmt->execute(array_values($filteredRow));
            $inserted++;
        } catch(Exception $e) {
            $errors++;
            if($errors <= 3) {
                echo "Error ID {$row['id']}: " . $e->getMessage() . "\n";
            }
        }
    }
    echo "Insertados: $inserted\n";
    echo "Errores: $errors\n";
}

echo "\nTotal cartons LOCAL ahora: " . $local->query("SELECT COUNT(*) FROM cartons")->fetchColumn() . "\n";
