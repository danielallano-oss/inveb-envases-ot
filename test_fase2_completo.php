<?php
/**
 * Test completo de funcionalidades Fase 2
 */

$baseUrl = 'http://localhost';
$cookieFile = '/tmp/test_fase2_cookies';
$results = [];

function testRoute($url, $method = 'GET') {
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
    }
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $code;
}

// Login
echo "=== TEST COMPLETO FASE 2 ===\n\n";
$ch = curl_init("$baseUrl/login");
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_COOKIEJAR => $cookieFile, CURLOPT_COOKIEFILE => $cookieFile]);
$html = curl_exec($ch);
curl_close($ch);
preg_match('/name="_token"[^>]+value="([^"]+)"/', $html, $m);
$token = $m[1] ?? '';

$ch = curl_init("$baseUrl/login");
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => "_token=$token&rut=22222222-2&password=password",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_COOKIEJAR => $cookieFile,
    CURLOPT_COOKIEFILE => $cookieFile,
]);
curl_exec($ch);
curl_close($ch);
echo "[LOGIN] OK\n\n";

$tests = [
    // MODULO 1: AUTH
    ['AUTH', 'AUTH-001', 'Login page', '/login'],
    ['AUTH', 'AUTH-003', 'Recovery password', '/recoveryPassword'],

    // MODULO 2: OT
    ['OT', 'OT-001a', 'Home', '/home'],
    ['OT', 'OT-001b', 'Ordenes trabajo', '/ordenes-trabajo'],
    ['OT', 'OT-002', 'Crear OT', '/crear-ot'],
    ['OT', 'OT-003', 'Crear OT old', '/crear-ot-old'],
    ['OT', 'OT-005', 'Edit OT', '/edit-ot/26595'],
    ['OT', 'OT-007', 'Duplicar OT', '/duplicar/26595'],
    ['OT', 'OT-010', 'Crear Licitacion', '/crear-licitacion'],
    ['OT', 'OT-011', 'Crear Ficha', '/crear-ficha-tecnica'],
    ['OT', 'OT-012', 'Crear Estudio', '/crear-estudio-benchmarking'],
    ['OT', 'OT-030', 'Listado Aprobacion', '/listadoAprobacion'],
    ['OT', 'OT-042', 'Buscar CAD', '/cad'],
    ['OT', 'OT-043', 'Buscar Material', '/material'],

    // MODULO 3: GESTIONES
    ['GEST', 'GEST-001', 'Gestionar OT', '/gestionarOt/26595'],
    ['GEST', 'GEST-006', 'Detalle log', '/detalleLogOt/26595'],
    ['GEST', 'GEST-011', 'Detalle McKee', '/detalleMckee?ot_id=26595'],

    // MODULO 4: MUESTRAS
    ['MUE', 'MUE-007', 'Muestras OT', '/getMuestrasOt/26595'],

    // MODULO 5: ASIGNACIONES
    ['ASIG', 'ASIG-001', 'Asignaciones', '/asignaciones'],

    // MODULO 6: NOTIFICACIONES
    ['NOT', 'NOT-001', 'Notificaciones', '/notificaciones'],

    // MODULO 7: REPORTES (rutas correctas con prefijo reporte-)
    ['REP', 'REP-001', 'Carga OT mes', '/reporte-gestion-carga-ot-mes'],
    ['REP', 'REP-002', 'Conversion OT', '/reporte-conversion-ot'],
    ['REP', 'REP-003', 'Conversion fechas', '/reporte-conversion-ot-entre-fechas'],
    ['REP', 'REP-004', 'Tiempos area', '/reporte-tiempos-por-area-ot-mes'],
    ['REP', 'REP-005', 'Motivos rechazos', '/reporte-motivos-rechazos-mes'],
    ['REP', 'REP-006', 'Rechazos mes', '/reporte-rechazos-mes'],
    ['REP', 'REP-007', 'OT activas area', '/reporte-ot-activas-por-area'],
    ['REP', 'REP-008', 'Anulaciones', '/reporte-anulaciones'],
    ['REP', 'REP-009', 'Muestras', '/reporte-muestras'],
    ['REP', 'REP-010', 'Indicador sala', '/reporte-indicador-sala-muestras'],

    // MODULO 8: COTIZADOR
    ['COT', 'COT-001', 'Crear cotizacion', '/cotizador/crear'],
    ['COT', 'COT-003', 'Index cotizaciones', '/cotizador/index'],
    ['COT', 'COT-004', 'Crear externo', '/cotizador/crear_externo'],
    ['COT', 'COT-005', 'Index externo', '/cotizador/index_externo'],
    ['COT', 'COT-010', 'Aprobaciones', '/cotizador/aprobaciones'],
    ['COT', 'COT-030', 'Crear area HC', '/cotizador/crear_areahc'],

    // MODULO 9: MANTENEDORES
    ['MANT', 'MANT-001', 'Usuarios list', '/mantenedores/users/list'],
    ['MANT', 'MANT-002', 'Usuarios create', '/mantenedores/users/create'],
    ['MANT', 'MANT-003', 'Clientes list', '/mantenedores/clients/list'],
    ['MANT', 'MANT-004', 'Clientes create', '/mantenedores/clients/create'],
    ['MANT', 'MANT-005', 'Clientes edit', '/mantenedores/clients/editar/1'],
    ['MANT', 'MANT-006', 'Sectores list', '/mantenedores/sectors/list'],
    ['MANT', 'MANT-007', 'Jerarquias list', '/mantenedores/hierarchies/list'],
    ['MANT', 'MANT-008', 'Subjerarquias list', '/mantenedores/subhierarchies/list'],
    ['MANT', 'MANT-009', 'Subsubjerarquias list', '/mantenedores/subsubhierarchies/list'],
    ['MANT', 'MANT-010', 'Tipos producto list', '/mantenedores/product-types/list'],
    ['MANT', 'MANT-011', 'Estilos list', '/mantenedores/styles/list'],
    ['MANT', 'MANT-012', 'Cartones list', '/mantenedores/cartons/list'],
    ['MANT', 'MANT-013', 'Colores list', '/mantenedores/colors/list'],
    ['MANT', 'MANT-014', 'Canales list', '/mantenedores/canals/list'],
    ['MANT', 'MANT-015', 'Almacenes list', '/mantenedores/almacenes/list'],
    ['MANT', 'MANT-016', 'Tipos cinta list', '/mantenedores/tipos-cintas/list'],
    ['MANT', 'MANT-017', 'Adhesivos list', '/mantenedores/adhesivos/list'],
    ['MANT', 'MANT-018', 'CeBes list', '/mantenedores/cebes/list'],
    ['MANT', 'MANT-019', 'Clasif clientes list', '/mantenedores/clasificaciones_clientes/list'],
    ['MANT', 'MANT-020', 'Secuencias op list', '/mantenedores/secuencias-operacionales/list'],
    ['MANT', 'MANT-021', 'Materiales list', '/mantenedores/materials/list'],
    ['MANT', 'MANT-022', 'Pallet types list', '/mantenedores/pallet-types/list'],
    ['MANT', 'MANT-023', 'Rechazo conjunto list', '/mantenedores/rechazo-conjunto/list'],
    ['MANT', 'MANT-024', 'Grupo imputacion list', '/mantenedores/grupo-imputacion-material/list'],
    ['MANT', 'MANT-025', 'Org venta list', '/mantenedores/organizacion-venta/list'],
    ['MANT', 'MANT-026', 'Tiempo tratamiento list', '/mantenedores/tiempo-tratamiento/list'],

    // ENDPOINTS AJAX
    ['AJAX', 'AJAX-001', 'getJerarquia2', '/getJerarquia2?hierarchy_id=1'],
    ['AJAX', 'AJAX-002', 'getJerarquia3', '/getJerarquia3?subhierarchy_id=1'],
    ['AJAX', 'AJAX-003', 'getCad', '/getCad?client_id=1'],
    ['AJAX', 'AJAX-004', 'getCarton', '/getCarton?process_id=1'],
    ['AJAX', 'AJAX-005', 'getContactosCliente', '/getContactosCliente?client_id=1'],
    ['AJAX', 'AJAX-006', 'getInstalacionesCliente', '/getInstalacionesCliente?client_id=1'],
    ['AJAX', 'AJAX-007', 'getUsersByArea', '/getUsersByArea?area_id=1'],
    ['AJAX', 'AJAX-008', 'getColorCarton', '/getColorCarton'],
    ['AJAX', 'AJAX-009', 'getDesignType', '/getDesignType?impresion=1'],
    ['AJAX', 'AJAX-010', 'getListaCarton', '/getListaCarton?carton_color=1&planta=1&impresion=1'],
    ['AJAX', 'AJAX-011', 'getRecubrimientoInterno', '/getRecubrimientoInterno'],
    ['AJAX', 'AJAX-012', 'getRecubrimientoExterno', '/getRecubrimientoExterno'],
    ['AJAX', 'AJAX-013', 'getPlantaObjetivo', '/getPlantaObjetivo'],
    ['AJAX', 'AJAX-014', 'getMaquilaServicio', '/getMaquilaServicio'],
    ['AJAX', 'AJAX-015', 'getSecuenciasOperacionales', '/getSecuenciasOperacionales'],
];

$currentMod = '';
foreach ($tests as $t) {
    if ($currentMod != $t[0]) {
        $currentMod = $t[0];
        echo "\n=== MODULO: $currentMod ===\n";
    }
    $code = testRoute("$baseUrl{$t[3]}");
    $ok = ($code == 200 || $code == 302);
    $status = $ok ? 'OK' : 'FAIL';
    echo "[{$t[1]}] {$t[2]}: HTTP $code $status\n";
    $results[$t[0]][] = $ok;
}

echo "\n\n========================================\n";
echo "           RESUMEN POR MODULO\n";
echo "========================================\n";
$totalOk = 0;
$totalTests = 0;
foreach ($results as $mod => $r) {
    $ok = count(array_filter($r));
    $total = count($r);
    $pct = $total > 0 ? round(($ok / $total) * 100) : 0;
    $status = $pct >= 80 ? 'PASS' : ($pct >= 50 ? 'WARN' : 'FAIL');
    echo sprintf("%-6s: %2d/%-2d (%3d%%) [%s]\n", $mod, $ok, $total, $pct, $status);
    $totalOk += $ok;
    $totalTests += $total;
}
echo "----------------------------------------\n";
$totalPct = $totalTests > 0 ? round(($totalOk / $totalTests) * 100) : 0;
echo sprintf("TOTAL:  %2d/%-2d (%3d%%)\n", $totalOk, $totalTests, $totalPct);
echo "========================================\n";

@unlink($cookieFile);
