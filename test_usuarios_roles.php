<?php
/**
 * Test de Usuarios por Rol - Fase 7.2b
 * Verifica que cada rol puede acceder a sus funcionalidades correspondientes
 */

$baseUrl = 'http://localhost';
$cookieFile = '/tmp/test_users_cookies';

function login($rut, $password = 'password') {
    global $baseUrl, $cookieFile;
    @unlink($cookieFile);

    $ch = curl_init("$baseUrl/login");
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_COOKIEJAR => $cookieFile, CURLOPT_COOKIEFILE => $cookieFile]);
    $html = curl_exec($ch);
    curl_close($ch);
    preg_match('/name="_token"[^>]+value="([^"]+)"/', $html, $m);
    $token = $m[1] ?? '';

    $ch = curl_init("$baseUrl/login");
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => "_token=$token&rut=$rut&password=$password",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_COOKIEJAR => $cookieFile,
        CURLOPT_COOKIEFILE => $cookieFile,
    ]);
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $code == 200 && strpos($response, 'Iniciar Sesión') === false;
}

function testRoute($url) {
    global $baseUrl, $cookieFile;
    $ch = curl_init("$baseUrl$url");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_COOKIEJAR => $cookieFile,
        CURLOPT_COOKIEFILE => $cookieFile,
        CURLOPT_TIMEOUT => 30,
    ]);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $code;
}

// Definición de usuarios de prueba por rol
$usuarios = [
    ['rut' => '22222222-2', 'role' => 'Administrador', 'role_id' => 1],
    ['rut' => '33333333-3', 'role' => 'Gerente', 'role_id' => 2],
    ['rut' => '23748870-9', 'role' => 'Jefe de Ventas', 'role_id' => 3],
    ['rut' => '11334692-2', 'role' => 'Vendedor', 'role_id' => 4],
    ['rut' => '20649380-1', 'role' => 'Jefe de Desarrollo', 'role_id' => 5],
    ['rut' => '8106237-4', 'role' => 'Ingeniero', 'role_id' => 6],
];

// Rutas a probar por tipo de acceso
$rutas_comunes = [
    '/home' => 'Dashboard',
    '/ordenes-trabajo' => 'Listado OT',
    '/notificaciones' => 'Notificaciones',
];

$rutas_ventas = [
    '/crear-ot' => 'Crear OT',
    '/cotizador/index' => 'Cotizaciones',
];

$rutas_admin = [
    '/mantenedores/users/list' => 'Usuarios',
    '/mantenedores/clients/list' => 'Clientes',
];

$rutas_reportes = [
    '/reporte-gestion-carga-ot-mes' => 'Reporte Carga OT',
    '/reporte-conversion-ot' => 'Reporte Conversión',
];

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║         TEST DE USUARIOS POR ROL - FASE 7.2b                ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

$resultados = [];
$totalPass = 0;
$totalFail = 0;

foreach ($usuarios as $usuario) {
    echo "┌─────────────────────────────────────────────────────────────┐\n";
    printf("│ Usuario: %-15s Rol: %-25s │\n", $usuario['rut'], $usuario['role']);
    echo "├─────────────────────────────────────────────────────────────┤\n";

    $loginOk = login($usuario['rut']);
    if (!$loginOk) {
        echo "│ ✗ Login fallido                                            │\n";
        echo "└─────────────────────────────────────────────────────────────┘\n\n";
        $totalFail++;
        continue;
    }
    echo "│ ✓ Login exitoso                                             │\n";
    $totalPass++;

    // Probar rutas comunes
    foreach ($rutas_comunes as $ruta => $nombre) {
        $code = testRoute($ruta);
        $ok = ($code == 200 || $code == 302);
        $status = $ok ? '✓' : '✗';
        printf("│ %s %-30s HTTP %-3d                  │\n", $status, $nombre, $code);
        if ($ok) $totalPass++; else $totalFail++;
    }

    // Probar rutas de ventas (roles 3, 4)
    if (in_array($usuario['role_id'], [1, 2, 3, 4, 5, 6])) {
        foreach ($rutas_ventas as $ruta => $nombre) {
            $code = testRoute($ruta);
            $ok = ($code == 200 || $code == 302);
            $status = $ok ? '✓' : '✗';
            printf("│ %s %-30s HTTP %-3d                  │\n", $status, $nombre, $code);
            if ($ok) $totalPass++; else $totalFail++;
        }
    }

    // Probar rutas admin (solo role 1)
    if ($usuario['role_id'] == 1) {
        foreach ($rutas_admin as $ruta => $nombre) {
            $code = testRoute($ruta);
            $ok = ($code == 200 || $code == 302);
            $status = $ok ? '✓' : '✗';
            printf("│ %s %-30s HTTP %-3d                  │\n", $status, $nombre, $code);
            if ($ok) $totalPass++; else $totalFail++;
        }
    }

    // Probar rutas de reportes (roles gerenciales)
    if (in_array($usuario['role_id'], [1, 2, 3, 5])) {
        foreach ($rutas_reportes as $ruta => $nombre) {
            $code = testRoute($ruta);
            $ok = ($code == 200 || $code == 302);
            $status = $ok ? '✓' : '✗';
            printf("│ %s %-30s HTTP %-3d                  │\n", $status, $nombre, $code);
            if ($ok) $totalPass++; else $totalFail++;
        }
    }

    echo "└─────────────────────────────────────────────────────────────┘\n\n";
}

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║                    RESUMEN DE PRUEBAS                        ║\n";
echo "╠══════════════════════════════════════════════════════════════╣\n";
$total = $totalPass + $totalFail;
$pct = $total > 0 ? round(($totalPass / $total) * 100) : 0;
printf("║  Tests exitosos: %-3d                                        ║\n", $totalPass);
printf("║  Tests fallidos: %-3d                                        ║\n", $totalFail);
printf("║  Porcentaje: %3d%%                                           ║\n", $pct);
echo "╠══════════════════════════════════════════════════════════════╣\n";
$estado = $pct >= 95 ? 'EXITOSO' : ($pct >= 80 ? 'PARCIAL' : 'FALLIDO');
printf("║  Estado: %s                                            ║\n", $estado);
echo "╚══════════════════════════════════════════════════════════════╝\n";

@unlink($cookieFile);
