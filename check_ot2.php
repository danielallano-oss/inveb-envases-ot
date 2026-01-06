<?php
$pdo = new PDO('mysql:host=inveb-mysql-compose;dbname=envases_ot', 'root', 'root');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== Columnas de work_orders ===\n";
$r = $pdo->query("SHOW COLUMNS FROM work_orders LIKE '%state%'");
while($row = $r->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . "\n";
}

$r = $pdo->query("SHOW COLUMNS FROM work_orders LIKE '%channel%'");
while($row = $r->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . "\n";
}

echo "\n=== OT 26590 ===\n";
$r = $pdo->query("SELECT * FROM work_orders WHERE id = 26590");
$row = $r->fetch(PDO::FETCH_ASSOC);
if($row) {
    echo "id: " . $row['id'] . "\n";
    echo "descripcion: " . $row['descripcion'] . "\n";
    echo "created_at: " . $row['created_at'] . "\n";
    // Buscar campos de estado y canal
    foreach($row as $k => $v) {
        if(strpos($k, 'state') !== false || strpos($k, 'channel') !== false || strpos($k, 'creador') !== false) {
            echo "$k: $v\n";
        }
    }
} else {
    echo "OT 26590 NO EXISTE\n";
}

echo "\n=== OT 26591 ===\n";
$r = $pdo->query("SELECT * FROM work_orders WHERE id = 26591");
$row = $r->fetch(PDO::FETCH_ASSOC);
if($row) {
    echo "id: " . $row['id'] . "\n";
    echo "descripcion: " . $row['descripcion'] . "\n";
    echo "created_at: " . $row['created_at'] . "\n";
    foreach($row as $k => $v) {
        if(strpos($k, 'state') !== false || strpos($k, 'channel') !== false || strpos($k, 'creador') !== false) {
            echo "$k: $v\n";
        }
    }
}

echo "\n=== Gestiones para ambas OTs ===\n";
$r = $pdo->query("SELECT work_order_id, COUNT(*) as cnt FROM managements WHERE work_order_id IN (26590, 26591) GROUP BY work_order_id");
while($row = $r->fetch(PDO::FETCH_ASSOC)) {
    echo "OT {$row['work_order_id']}: {$row['cnt']} gestiones\n";
}
