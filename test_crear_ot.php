<?php
/**
 * Test crear OT post-correcciones Fase 6
 */

$baseUrl = 'http://localhost';
$cookieFile = '/tmp/cookie_ot_test';

echo "=== TEST CREAR OT POST-CORRECCIONES ===\n\n";

// 1. Login
$loginPage = file_get_contents("$baseUrl/login");
preg_match('/<input[^>]+name="_token"[^>]+value="([^"]+)"/', $loginPage, $m);
$token = $m[1] ?? '';

$ch = curl_init("$baseUrl/login");
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        '_token' => $token,
        'rut' => '22222222-2',
        'password' => 'password'
    ]),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_COOKIEJAR => $cookieFile,
    CURLOPT_COOKIEFILE => $cookieFile,
]);
curl_exec($ch);
curl_close($ch);
echo "[1] Login OK\n";

// 2. Obtener formulario crear-ot y token CSRF
$ch2 = curl_init("$baseUrl/crear-ot");
curl_setopt_array($ch2, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR => $cookieFile,
    CURLOPT_COOKIEFILE => $cookieFile,
]);
$formHtml = curl_exec($ch2);
$httpCode = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
curl_close($ch2);

if ($httpCode != 200) {
    echo "[ERROR] Formulario /crear-ot retorno HTTP $httpCode\n";
    exit(1);
}
echo "[2] Formulario /crear-ot carga OK (HTTP 200)\n";

// Extraer token CSRF del formulario
preg_match('/<input[^>]+name="_token"[^>]+value="([^"]+)"/', $formHtml, $m2);
$csrfToken = $m2[1] ?? '';

if (empty($csrfToken)) {
    echo "[ERROR] No se pudo extraer token CSRF\n";
    exit(1);
}
echo "[3] Token CSRF extraido\n";

// 3. Preparar datos para crear OT tipo Licitacion (tipo_solicitud=6)
$otData = [
    '_token' => $csrfToken,
    'tipo_solicitud' => '6',
    'ajuste_area_desarrollo' => '1',
    'client_id' => '1',
    'descripcion' => 'TEST OT FASE 6 ' . date('Y-m-d H:i'),
    'canal_id' => '1',
    'observacion' => 'OT prueba Fase 6'
];

// 4. Enviar formulario
$ch3 = curl_init("$baseUrl/guardar-ot");
curl_setopt_array($ch3, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($otData),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_COOKIEJAR => $cookieFile,
    CURLOPT_COOKIEFILE => $cookieFile,
]);
$response = curl_exec($ch3);
$httpCode = curl_getinfo($ch3, CURLINFO_HTTP_CODE);
$finalUrl = curl_getinfo($ch3, CURLINFO_EFFECTIVE_URL);
curl_close($ch3);

echo "[4] Formulario enviado\n";
echo "    HTTP Code: $httpCode\n";
echo "    URL Final: $finalUrl\n";

// 5. Verificar resultado
if ($httpCode == 200 && strpos($finalUrl, 'ordenes-trabajo') !== false) {
    echo "\n[RESULTADO] OT CREADA EXITOSAMENTE\n";
} elseif ($httpCode == 500) {
    echo "\n[ERROR] HTTP 500 - Error del servidor\n";
} else {
    echo "\n[RESULTADO] HTTP $httpCode - Verificar en BD\n";
}

@unlink($cookieFile);
