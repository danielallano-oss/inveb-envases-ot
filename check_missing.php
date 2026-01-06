<?php
$qas = new PDO("mysql:host=envases-ot.inveb.cl;dbname=envases_ot", "tandina", "1a35a2f5a454526a7fb54f98da4117f0");
$local = new PDO("mysql:host=inveb-mysql-compose;dbname=envases_ot", "root", "root");

// Tablas a verificar para los campos faltantes
$tables = [
    'reference_types' => 'REFERENCIA (tipo)',
    'materials' => 'REFERENCIA (materiales)',
    'impresion' => 'IMPRESIÓN',
    'carton_colors' => 'COLOR CARTÓN',
    'cartons' => 'CARTÓN',
    'product_type_developing' => 'TIPO PRODUCTO (desarrollo)',
    'product_types' => 'TIPO ITEM',
    'tipos_cintas' => 'CINTA',
    'colors' => 'COLORES'
];

echo "=== VERIFICACIÓN DE CAMPOS FALTANTES ===\n\n";

foreach($tables as $table => $desc) {
    echo "--- $desc ($table) ---\n";

    // QAS
    try {
        $qasCount = $qas->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        echo "QAS: $qasCount registros\n";
    } catch(Exception $e) {
        echo "QAS: NO EXISTE\n";
        $qasCount = 0;
    }

    // Local
    try {
        $localCount = $local->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        echo "LOCAL: $localCount registros\n";
    } catch(Exception $e) {
        echo "LOCAL: NO EXISTE\n";
        $localCount = -1;
    }

    // Mostrar muestra si hay datos en QAS
    if($qasCount > 0 && $localCount <= 0) {
        try {
            $sample = $qas->query("SELECT * FROM $table LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
            echo "Muestra QAS:\n";
            foreach($sample as $row) {
                $display = [];
                foreach($row as $k => $v) {
                    if(in_array($k, ['id', 'nombre', 'descripcion', 'codigo', 'name'])) {
                        $display[] = "$k: $v";
                    }
                }
                echo "  " . implode(", ", $display) . "\n";
            }
        } catch(Exception $e) {}
    }
    echo "\n";
}

// Verificar códigos de producto en work_orders
echo "=== CÓDIGOS DE PRODUCTO EN OTs LOCALES ===\n";
$r = $local->query("SELECT id, codigo_producto, descripcion FROM work_orders WHERE codigo_producto IS NOT NULL AND codigo_producto != '' LIMIT 10");
while($row = $r->fetch(PDO::FETCH_ASSOC)) {
    echo "OT {$row['id']}: {$row['codigo_producto']} - {$row['descripcion']}\n";
}
