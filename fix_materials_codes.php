<?php
$pdo = new PDO('mysql:host=inveb-mysql-compose;dbname=envases_ot', 'root', 'root');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== Verificando materials_codes ===\n";

// Verificar si existe la tabla
try {
    $r = $pdo->query("SELECT COUNT(*) FROM materials_codes");
    $count = $r->fetchColumn();
    echo "LOCAL materials_codes: $count registros\n";
} catch(Exception $e) {
    echo "Tabla no existe, creándola...\n";
    $pdo->exec("CREATE TABLE materials_codes (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL
    ) AUTO_INCREMENT = 700000");
    $count = 0;
}

if($count == 0) {
    echo "Insertando registro inicial con ID 700000...\n";
    $pdo->exec("INSERT INTO materials_codes (id, created_at, updated_at) VALUES (700000, NOW(), NOW())");
    echo "Registro creado\n";
}

// Verificar
$r = $pdo->query("SELECT * FROM materials_codes ORDER BY id DESC LIMIT 5");
echo "\n=== Registros en materials_codes ===\n";
while($row = $r->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: {$row['id']}\n";
}
