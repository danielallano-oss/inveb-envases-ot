<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PdfController extends Controller
{
    /**
     * Procesar archivo PDF
     * TODO: Implementar funcionalidad de procesamiento PDF si es requerida
     */
    public function procesarArchivo(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Funcionalidad de procesamiento PDF no implementada'
        ], 501);
    }
}
