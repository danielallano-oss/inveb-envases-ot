<?php
$pdo = new PDO('mysql:host=inveb-mysql-compose;dbname=envases_ot', 'root', 'root');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Hacer observacion también nullable por si acaso
echo "=== Ajustando columna observacion ===\n";
try {
    $pdo->exec("ALTER TABLE managements MODIFY COLUMN observacion VARCHAR(255) NULL DEFAULT ''");
    echo "OK\n";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Verificar si work_order_id 26590 se creó
echo "\n=== Verificando OT 26590 ===\n";
try {
    $r = $pdo->query("SELECT id, descripcion, state_id FROM work_orders WHERE id = 26590");
    $row = $r->fetch(PDO::FETCH_ASSOC);
    if($row) {
        echo "OT 26590 existe: " . $row['descripcion'] . " (estado: {$row['state_id']})\n";
    } else {
        echo "OT 26590 no existe\n";
    }
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Obtener el máximo ID para ver qué OTs hay
echo "\n=== Últimas OTs creadas ===\n";
$r = $pdo->query("SELECT id, descripcion, created_at FROM work_orders ORDER BY id DESC LIMIT 5");
while($row = $r->fetch(PDO::FETCH_ASSOC)) {
    echo "OT {$row['id']}: {$row['descripcion']} - {$row['created_at']}\n";
}
