<?php
require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ot = App\Ot::find(26595);
if ($ot) {
    echo "OT ID: " . $ot->id . "\n";
    echo "tipo_solicitud: " . $ot->tipo_solicitud . "\n";
} else {
    echo "OT 26595 not found\n";
}
