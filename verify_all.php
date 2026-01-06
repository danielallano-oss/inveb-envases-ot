<?php
$local = new PDO("mysql:host=inveb-mysql-compose;dbname=envases_ot", "root", "root");

echo "=== ESTADO FINAL DE CATÁLOGOS PARA COMBOS ===\n\n";

$tables = [
    // Combos principales
    'reference_types' => 'REFERENCIA (tipo)',
    'materials' => 'MATERIALES/CÓDIGOS',
    'impresion' => 'IMPRESIÓN',
    'colors' => 'COLORES',
    'cartons' => 'CARTÓN',
    'carton_esquineros' => 'ESQUINEROS',
    'product_type_developing' => 'TIPO PRODUCTO (desarrollo)',
    'product_types' => 'TIPO ITEM',
    'tipos_cintas' => 'CINTA',
    // Otros catálogos
    'org_ventas' => 'ORG VENTAS',
    'plants' => 'PLANTAS',
    'clients' => 'CLIENTES',
    'users' => 'USUARIOS',
    'work_order_states' => 'ESTADOS OT',
    'channels' => 'CANALES',
    'expected_uses' => 'USOS ESPERADOS',
    'target_markets' => 'MERCADOS'
];

foreach($tables as $t => $desc) {
    try {
        $c = $local->query("SELECT COUNT(*) FROM $t")->fetchColumn();
        $status = $c > 0 ? '✓' : '✗';
        echo "$status $desc ($t): $c registros\n";
    } catch(Exception $e) {
        echo "✗ $desc ($t): NO EXISTE\n";
    }
}

echo "\n=== CÓDIGOS DE PRODUCTO DISPONIBLES ===\n";
$r = $local->query("SELECT id, codigo FROM materials WHERE codigo IS NOT NULL AND codigo != '' LIMIT 10");
while($row = $r->fetch(PDO::FETCH_ASSOC)) {
    echo "Material ID {['id']}: {['codigo']}\n";
}
