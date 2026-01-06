<?php
$pdo = new PDO('mysql:host=inveb-mysql-compose;dbname=envases_ot', 'root', 'root');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== Estructura actual de managements ===\n";
$r = $pdo->query('DESCRIBE managements');
while($row = $r->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . ' | ' . $row['Type'] . ' | Null: ' . $row['Null'] . ' | Default: ' . ($row['Default'] ?? 'NULL') . "\n";
}

echo "\n=== Modificando columna titulo ===\n";
try {
    $pdo->exec("ALTER TABLE managements MODIFY COLUMN titulo VARCHAR(255) NULL DEFAULT ''");
    echo "Columna titulo modificada para permitir NULL y valor por defecto vacío\n";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Estructura después del cambio ===\n";
$r = $pdo->query("SHOW COLUMNS FROM managements WHERE Field = 'titulo'");
$row = $r->fetch(PDO::FETCH_ASSOC);
print_r($row);
