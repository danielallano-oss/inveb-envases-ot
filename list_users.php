<?php
$local = new PDO("mysql:host=inveb-mysql-compose;dbname=envases_ot", "root", "root");

echo "=== USUARIOS POR ROL ===\n\n";

$query = "
SELECT r.id as role_id, r.nombre as rol, u.id, u.email, u.nombre, u.apellido, u.active
FROM users u
JOIN roles r ON u.role_id = r.id
ORDER BY r.id, u.active DESC, u.id
";

$rows = $local->query($query)->fetchAll(PDO::FETCH_ASSOC);

$currentRole = null;
foreach($rows as $row) {
    if($currentRole !== $row['role_id']) {
        $currentRole = $row['role_id'];
        echo "\n--- {$row['rol']} (role_id: $currentRole) ---\n";
    }
    $status = $row['active'] == 1 ? '' : '[INACTIVO]';
    echo "  ID: {$row['id']} | {$row['email']} | {$row['nombre']} {$row['apellido']} $status\n";
}

echo "\n\n=== NOTA ===\n";
echo "Para iniciar sesion como otro usuario, usa la funcion 'Iniciar Sesion' desde el mantenedor de usuarios (solo disponible para usuario ID 1).\n";
echo "O actualiza la contrasena de un usuario con:\n";
echo "UPDATE users SET password = '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE id = X;\n";
echo "(esto establece la contrasena como 'password')\n";
