<?php
/**
 * Script de verificacion de entorno local
 * Verifica que todas las tablas necesarias tengan datos
 */

$local = new PDO("mysql:host=inveb-mysql-compose;dbname=envases_ot", "root", "root");

$tables = [
    // Tablas principales
    'users' => ['min' => 1, 'desc' => 'Usuarios del sistema'],
    'roles' => ['min' => 18, 'desc' => 'Roles de usuario'],
    'clients' => ['min' => 10, 'desc' => 'Clientes'],
    'plantas' => ['min' => 3, 'desc' => 'Plantas'],
    'canals' => ['min' => 5, 'desc' => 'Canales de venta'],
    'cotizacion_estados' => ['min' => 5, 'desc' => 'Estados de cotizacion'],

    // Catalogos para combos
    'product_types' => ['min' => 5, 'desc' => 'Tipos de producto'],
    'impresion' => ['min' => 7, 'desc' => 'Tipos de impresion'],
    'fsc' => ['min' => 7, 'desc' => 'Certificaciones FSC'],
    'tipos_cintas' => ['min' => 2, 'desc' => 'Tipos de cinta'],
    'colors' => ['min' => 1000, 'desc' => 'Colores'],
    'cartons' => ['min' => 200, 'desc' => 'Cartones'],
    'carton_esquineros' => ['min' => 10, 'desc' => 'Esquineros'],
    'materials' => ['min' => 2000, 'desc' => 'Materiales'],
    'reference_types' => ['min' => 1, 'desc' => 'Tipos de referencia'],
    'product_type_developing' => ['min' => 4, 'desc' => 'Tipos desarrollo'],

    // Tablas de control (CRITICAS)
    'relacion_filtro_ingresos_principales' => ['min' => 75, 'desc' => 'Relaciones cascada (CRITICO)'],
    'materials_codes' => ['min' => 1, 'desc' => 'Secuencia codigos material'],
];

echo "=== VERIFICACION DE ENTORNO LOCAL ===\n";
echo "Fecha: " . date('Y-m-d H:i:s') . "\n\n";

$errors = 0;
$warnings = 0;

foreach($tables as $table => $config) {
    try {
        $result = $local->query("SELECT COUNT(*) FROM $table");
        if($result === false) {
            printf("%-45s %6s  [ERROR] NO EXISTE\n", $config['desc'] . " ($table)", '-');
            $errors++;
            continue;
        }
        $count = $result->fetchColumn();
        $status = '';

        if($count == 0) {
            $status = '[ERROR] VACIO';
            $errors++;
        } elseif($count < $config['min']) {
            $status = '[WARN] Bajo (' . $config['min'] . ' esperados)';
            $warnings++;
        } else {
            $status = '[OK]';
        }

        printf("%-45s %6d  %s\n", $config['desc'] . " ($table)", $count, $status);

    } catch(Exception $e) {
        printf("%-45s %6s  [ERROR] NO EXISTE\n", $config['desc'] . " ($table)", '-');
        $errors++;
    }
}

echo "\n=== RESUMEN ===\n";
echo "Errores: $errors\n";
echo "Advertencias: $warnings\n";

if($errors > 0) {
    echo "\n[!] HAY ERRORES CRITICOS. Ejecute los scripts de sincronizacion.\n";
    exit(1);
} elseif($warnings > 0) {
    echo "\n[!] Hay advertencias. Revise las tablas con conteo bajo.\n";
    exit(0);
} else {
    echo "\n[OK] Entorno configurado correctamente.\n";
    exit(0);
}
