<?php

namespace App\Http\Controllers;

use App\Canal;
use Illuminate\Http\Request;

class CanalController extends Controller
{
    public function index()
    {
        //filtros:
        $canals_filter = Canal::all();
        //filters:
        $query = Canal::query();
        if (!is_null(request()->query('id'))) {
            $query = $query->whereIn('id', request()->query('id'));
        }
        // if (!is_null(request()->query('role_id'))) {
        //     $query = $query->whereIn('role_id', request()->query('role_id'));
        // }
        if (!is_null(request()->query('active'))) {
            $query = $query->whereIn('active', request()->query('active'));
        }
        /*order columnns table*/
        $orderby = trim(request()->query('orderby'));
        $sorted  = trim(request()->query('sorted'));
        $orderby = in_array($orderby, ['nombre']) ? $orderby : 'nombre';
        $sorted  = in_array($sorted, ['ASC', 'DESC']) ? $sorted : 'ASC';
        $canals = $query->orderBy($orderby, $sorted)->paginate(20);

        return view('canals.index', compact('canals', 'canals_filter'));
    }
    
    public function create()
    {
        return view('canals.create');
    }

    public function store(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'nombre' => 'required',
            'codigo' => 'required',
        ]);
        $canal = new Canal();
        $canal->nombre   = (trim($request->input('nombre')) != '') ? $request->input('nombre') : $canal->nombre;
        $canal->codigo   = (trim($request->input('codigo')) != '') ? $request->input('codigo') : $canal->codigo;
        $canal->save();
        return redirect()->route('mantenedores.canals.list')->with('success', 'Canal creado correctamente.');
    }

    public function edit($id)
    {
        $canal = Canal::find($id);
        return view('canals.edit', compact('canal'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'nombre' => 'required',
            'codigo' => 'required',
        ]);

        $canal = Canal::find($id);
        $canal->nombre = (trim($request->input('nombre')));
        $canal->codigo = (trim($request->input('codigo')));
        $canal->save();
        return redirect()->route('mantenedores.canals.list')->with('success', 'Canal editado correctamente.');
    }

    public function active($id)
    {
        Canal::findOrFail($id)->update(['active' => 1]);
        return redirect()->route('mantenedores.canals.list')->with('success', 'Canal activado correctamente.');
    }

    public function inactive($id)
    {
        Canal::findOrFail($id)->update(['active' => 0]);
        return redirect()->route('mantenedores.canals.list')->with('success', 'Canal inactivada correctamente.');
    }
}
