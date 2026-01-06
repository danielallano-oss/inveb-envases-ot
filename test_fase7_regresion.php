<?php
/**
 * Test de Regresión Completo - Fase 7
 * Verifica todas las funcionalidades del sistema INVEB
 */

$baseUrl = 'http://localhost';
$cookieFile = '/tmp/test_fase7_cookies';
$results = [];
$startTime = microtime(true);

function testRoute($url, $method = 'GET', $data = null) {
    global $cookieFile;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_COOKIEJAR => $cookieFile,
        CURLOPT_COOKIEFILE => $cookieFile,
        CURLOPT_TIMEOUT => 30,
    ]);
    if ($method == 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
    }
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $time = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
    curl_close($ch);
    return ['code' => $code, 'time' => $time, 'size' => strlen($response)];
}

function login($rut, $password) {
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
    return $code == 200 && strpos($response, 'login') === false;
}

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║      TEST DE REGRESIÓN COMPLETO - FASE 7 INVEB              ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// Test con usuario admin (rut: 22222222-2)
echo "▶ Iniciando sesión como administrador...\n";
$loginOk = login('22222222-2', 'password');
echo $loginOk ? "  ✓ Login exitoso\n\n" : "  ✗ Login fallido\n\n";

$tests = [
    // MÓDULO 1: AUTENTICACIÓN
    ['AUTH', 'Login Page', '/login'],
    ['AUTH', 'Recovery Password', '/recoveryPassword'],

    // MÓDULO 2: ÓRDENES DE TRABAJO
    ['OT', 'Home/Dashboard', '/home'],
    ['OT', 'Listado OT', '/ordenes-trabajo'],
    ['OT', 'Crear OT (nuevo)', '/crear-ot'],
    ['OT', 'Crear OT (antiguo)', '/crear-ot-old'],
    ['OT', 'Editar OT', '/edit-ot/26595'],
    ['OT', 'Duplicar OT', '/duplicar/26595'],
    ['OT', 'Crear Licitación', '/crear-licitacion'],
    ['OT', 'Crear Ficha Técnica', '/crear-ficha-tecnica'],
    ['OT', 'Crear Estudio', '/crear-estudio-benchmarking'],
    ['OT', 'Listado Aprobación', '/listadoAprobacion'],
    ['OT', 'Buscar CAD', '/cad'],
    ['OT', 'Buscar Material', '/material'],

    // MÓDULO 3: GESTIÓN OT
    ['GEST', 'Gestionar OT', '/gestionarOt/26595'],
    ['GEST', 'Detalle Log OT', '/detalleLogOt/26595'],
    ['GEST', 'Detalle McKee', '/detalleMckee?ot_id=26595'],

    // MÓDULO 4: MUESTRAS
    ['MUE', 'Muestras OT', '/getMuestrasOt/26595'],

    // MÓDULO 5: ASIGNACIONES
    ['ASIG', 'Asignaciones', '/asignaciones'],

    // MÓDULO 6: NOTIFICACIONES
    ['NOT', 'Notificaciones', '/notificaciones'],

    // MÓDULO 7: REPORTES
    ['REP', 'Carga OT Mes', '/reporte-gestion-carga-ot-mes'],
    ['REP', 'Conversión OT', '/reporte-conversion-ot'],
    ['REP', 'Conversión Entre Fechas', '/reporte-conversion-ot-entre-fechas'],
    ['REP', 'Tiempos por Área', '/reporte-tiempos-por-area-ot-mes'],
    ['REP', 'Motivos Rechazos', '/reporte-motivos-rechazos-mes'],
    ['REP', 'Rechazos Mes', '/reporte-rechazos-mes'],
    ['REP', 'OT Activas por Área', '/reporte-ot-activas-por-area'],
    ['REP', 'Anulaciones', '/reporte-anulaciones'],
    ['REP', 'Muestras', '/reporte-muestras'],
    ['REP', 'Indicador Sala', '/reporte-indicador-sala-muestras'],

    // MÓDULO 8: COTIZADOR
    ['COT', 'Crear Cotización', '/cotizador/crear'],
    ['COT', 'Index Cotizaciones', '/cotizador/index'],
    ['COT', 'Crear Externo', '/cotizador/crear_externo'],
    ['COT', 'Index Externo', '/cotizador/index_externo'],
    ['COT', 'Aprobaciones', '/cotizador/aprobaciones'],
    ['COT', 'Crear Área HC', '/cotizador/crear_areahc'],

    // MÓDULO 9: MANTENEDORES
    ['MANT', 'Usuarios List', '/mantenedores/users/list'],
    ['MANT', 'Usuarios Create', '/mantenedores/users/create'],
    ['MANT', 'Clientes List', '/mantenedores/clients/list'],
    ['MANT', 'Clientes Create', '/mantenedores/clients/create'],
    ['MANT', 'Clientes Edit', '/mantenedores/clients/editar/1'],
    ['MANT', 'Sectores', '/mantenedores/sectors/list'],
    ['MANT', 'Jerarquías', '/mantenedores/hierarchies/list'],
    ['MANT', 'Subjerarquías', '/mantenedores/subhierarchies/list'],
    ['MANT', 'Subsubjerarquías', '/mantenedores/subsubhierarchies/list'],
    ['MANT', 'Tipos Producto', '/mantenedores/product-types/list'],
    ['MANT', 'Estilos', '/mantenedores/styles/list'],
    ['MANT', 'Cartones', '/mantenedores/cartons/list'],
    ['MANT', 'Colores', '/mantenedores/colors/list'],
    ['MANT', 'Canales', '/mantenedores/canals/list'],
    ['MANT', 'Almacenes', '/mantenedores/almacenes/list'],
    ['MANT', 'Tipos Cinta', '/mantenedores/tipos-cintas/list'],
    ['MANT', 'Adhesivos', '/mantenedores/adhesivos/list'],
    ['MANT', 'CeBes', '/mantenedores/cebes/list'],
    ['MANT', 'Clasificación Clientes', '/mantenedores/clasificaciones_clientes/list'],
    ['MANT', 'Secuencias Operacionales', '/mantenedores/secuencias-operacionales/list'],
    ['MANT', 'Materiales', '/mantenedores/materials/list'],
    ['MANT', 'Tipos Pallet', '/mantenedores/pallet-types/list'],
    ['MANT', 'Rechazo Conjunto', '/mantenedores/rechazo-conjunto/list'],
    ['MANT', 'Grupo Imputación', '/mantenedores/grupo-imputacion-material/list'],
    ['MANT', 'Org Venta', '/mantenedores/organizacion-venta/list'],
    ['MANT', 'Tiempo Tratamiento', '/mantenedores/tiempo-tratamiento/list'],

    // MÓDULO 10: AJAX ENDPOINTS
    ['AJAX', 'getJerarquia2', '/getJerarquia2?hierarchy_id=1'],
    ['AJAX', 'getJerarquia3', '/getJerarquia3?subhierarchy_id=1'],
    ['AJAX', 'getCad', '/getCad?client_id=1'],
    ['AJAX', 'getCarton', '/getCarton?process_id=1'],
    ['AJAX', 'getContactosCliente', '/getContactosCliente?client_id=1'],
    ['AJAX', 'getInstalacionesCliente', '/getInstalacionesCliente?client_id=1'],
    ['AJAX', 'getUsersByArea', '/getUsersByArea?area_id=1'],
    ['AJAX', 'getColorCarton', '/getColorCarton'],
    ['AJAX', 'getDesignType', '/getDesignType?impresion=1'],
    ['AJAX', 'getListaCarton', '/getListaCarton?carton_color=1&planta=1&impresion=1'],
    ['AJAX', 'getRecubrimientoInterno', '/getRecubrimientoInterno'],
    ['AJAX', 'getRecubrimientoExterno', '/getRecubrimientoExterno'],
    ['AJAX', 'getPlantaObjetivo', '/getPlantaObjetivo'],
    ['AJAX', 'getMaquilaServicio', '/getMaquilaServicio'],
    ['AJAX', 'getSecuenciasOperacionales', '/getSecuenciasOperacionales'],
];

$currentMod = '';
$moduleResults = [];
$totalTime = 0;
$passCount = 0;
$failCount = 0;

foreach ($tests as $t) {
    if ($currentMod != $t[0]) {
        $currentMod = $t[0];
        echo "\n┌─ MÓDULO: $currentMod ─────────────────────────────────────────┐\n";
    }

    $result = testRoute("$baseUrl{$t[2]}");
    $ok = ($result['code'] == 200 || $result['code'] == 302);
    $status = $ok ? '✓' : '✗';
    $timeStr = sprintf("%.2fs", $result['time']);

    if ($ok) {
        $passCount++;
    } else {
        $failCount++;
    }

    $totalTime += $result['time'];
    $moduleResults[$t[0]][] = $ok;

    printf("│ %s %-30s HTTP %-3d %s │\n", $status, substr($t[1], 0, 30), $result['code'], $timeStr);
}

echo "\n\n╔══════════════════════════════════════════════════════════════╗\n";
echo "║                    RESUMEN DE REGRESIÓN                      ║\n";
echo "╠══════════════════════════════════════════════════════════════╣\n";

$grandTotal = 0;
$grandPass = 0;
foreach ($moduleResults as $mod => $r) {
    $ok = count(array_filter($r));
    $total = count($r);
    $pct = $total > 0 ? round(($ok / $total) * 100) : 0;
    $status = $pct == 100 ? '✓' : ($pct >= 80 ? '~' : '✗');
    printf("║  %s %-8s %2d/%-2d (%3d%%)                                   ║\n", $status, $mod, $ok, $total, $pct);
    $grandTotal += $total;
    $grandPass += $ok;
}

echo "╠══════════════════════════════════════════════════════════════╣\n";
$grandPct = $grandTotal > 0 ? round(($grandPass / $grandTotal) * 100) : 0;
$finalStatus = $grandPct == 100 ? 'EXITOSO' : ($grandPct >= 80 ? 'PARCIAL' : 'FALLIDO');
printf("║  TOTAL: %2d/%-2d (%3d%%) - %s                          ║\n", $grandPass, $grandTotal, $grandPct, $finalStatus);
echo "╠══════════════════════════════════════════════════════════════╣\n";
$execTime = microtime(true) - $startTime;
printf("║  Tiempo total: %.2f segundos                               ║\n", $execTime);
printf("║  Tiempo promedio por test: %.3f segundos                   ║\n", $grandTotal > 0 ? $totalTime / $grandTotal : 0);
echo "╚══════════════════════════════════════════════════════════════╝\n";

if ($failCount > 0) {
    echo "\n⚠ TESTS FALLIDOS: $failCount\n";
}

@unlink($cookieFile);
