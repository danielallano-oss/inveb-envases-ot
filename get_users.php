<?php
require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = DB::table('users')
    ->join('roles', 'users.role_id', '=', 'roles.id')
    ->select('users.id', 'users.nombre', 'users.apellido', 'users.rut', 'users.role_id', 'roles.nombre as role_name')
    ->where('users.active', 1)
    ->limit(15)
    ->get();

echo "ID|RUT|ROLE_ID|ROLE_NAME|NOMBRE\n";
echo "---|---|---|---|---\n";
foreach($users as $u) {
    echo "{$u->id}|{$u->rut}|{$u->role_id}|{$u->role_name}|{$u->nombre} {$u->apellido}\n";
}
