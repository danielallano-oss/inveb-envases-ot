<?php

namespace App\Http\Controllers;

use App\ClasificacionCliente;
use Illuminate\Http\Request;

class ClasificacionClienteController extends Controller
{
    public function index()
    {
        //filtros:
        $clasificaciones_clientes_filter = ClasificacionCliente::all();
        //filters:
        $query = ClasificacionCliente::query();
        if (!is_null(request()->query('id'))) {
            $query = $query->whereIn('id', request()->query('id'));
        }
        if (!is_null(request()->query('active'))) {
            $query = $query->whereIn('active', request()->query('active'));
        }
        /*order columnns table*/
        $orderby = trim(request()->query('orderby'));
        $sorted  = trim(request()->query('sorted'));
        $orderby = in_array($orderby, ['id', 'name']) ? $orderby : 'name';
        $sorted  = in_array($sorted, ['ASC', 'DESC']) ? $sorted : 'ASC';
        $clasificaciones_clientes = $query->orderBy($orderby, $sorted)->paginate(20);

        return view('clasificaciones_clientes.index', compact('clasificaciones_clientes', 'clasificaciones_clientes_filter'));
    }

    public function create()
    {
        return view('clasificaciones_clientes.create');
    }

    public function store(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'name' => 'required'
        ]);

        $clasificacion_cliente = new ClasificacionCliente();
        $clasificacion_cliente->name    = (trim($request->input('name')) != '') ? $request->input('name') : $clasificacion_cliente->codigo;
        $clasificacion_cliente->save();
        return redirect()->route('mantenedores.clasificaciones_clientes.list')->with('success', 'Clasificación de Cliente creada correctamente.');
    }

    public function edit($id)
    {
        $clasificacion_cliente = ClasificacionCliente::find($id);
        return view('clasificaciones_clientes.edit', compact('clasificacion_cliente'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:clasificacion_clientes,name,' . $id,            
        ]);

        $clasificacion_cliente = ClasificacionCliente::find($id);
        $clasificacion_cliente->name    = (trim($request->input('name')));
        $clasificacion_cliente->save();

        return redirect()->route('mantenedores.clasificaciones_clientes.list')->with('success', 'Clasificacion de Cliente editada correctamente.');
    }

    public function active($id)
    {
        ClasificacionCliente::findOrFail($id)->update(['active' => 1]);
        return redirect()->route('mantenedores.clasificaciones_clientes.list')->with('success', 'Clasificación de Cliente activada correctamente.');
    }

    public function inactive($id)
    {
        ClasificacionCliente::findOrFail($id)->update(['active' => 0]);
        return redirect()->route('mantenedores.clasificaciones_clientes.list')->with('success', 'Clasificación de Cliente inactivada correctamente.');
    }
}
