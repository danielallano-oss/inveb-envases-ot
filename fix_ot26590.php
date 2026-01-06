<?php
$pdo = new PDO('mysql:host=inveb-mysql-compose;dbname=envases_ot', 'root', 'root');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== Creando gestión faltante para OT 26590 ===\n";

// Verificar qué gestión tiene la OT 26591 para copiar el patrón
$r = $pdo->query("SELECT * FROM managements WHERE work_order_id = 26591 LIMIT 1");
$template = $r->fetch(PDO::FETCH_ASSOC);
echo "Template de OT 26591:\n";
print_r($template);

// Insertar gestión para OT 26590
$sql = "INSERT INTO managements (titulo, observacion, management_type_id, user_id, work_order_id, work_space_id, duracion_segundos, state_id, created_at, updated_at) 
        VALUES ('', 'Creación de Órden de Trabajo', ?, ?, 26590, ?, 0, ?, NOW(), NOW())";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    $template['management_type_id'],
    $template['user_id'],
    $template['work_space_id'],
    $template['state_id']
]);

echo "\nGestión creada para OT 26590\n";

// Verificar
$r = $pdo->query("SELECT COUNT(*) FROM managements WHERE work_order_id = 26590");
echo "Gestiones OT 26590: " . $r->fetchColumn() . "\n";
