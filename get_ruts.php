<?php
$local = new PDO("mysql:host=inveb-mysql-compose;dbname=envases_ot", "root", "root");

$query = "
SELECT u.id, u.rut, u.email, u.nombre, u.apellido, r.nombre as rol
FROM users u
JOIN roles r ON u.role_id = r.id
WHERE u.id BETWEEN 1 AND 12
ORDER BY u.id
";

$rows = $local->query($query)->fetchAll(PDO::FETCH_ASSOC);

echo "=== USUARIOS DE PRUEBA (password: password) ===\n\n";
echo str_pad("ID", 4) . str_pad("RUT", 15) . str_pad("Rol", 30) . "Nombre\n";
echo str_repeat("-", 80) . "\n";

foreach($rows as $row) {
    echo str_pad($row['id'], 4);
    echo str_pad($row['rut'], 15);
    echo str_pad($row['rol'], 30);
    echo $row['nombre'] . " " . $row['apellido'] . "\n";
}
