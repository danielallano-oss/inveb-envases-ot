<?php

namespace App\Http\Controllers;

use App\TipoCinta;
use Illuminate\Http\Request;

class TipoCintaController extends Controller
{
    public function index()
    {
        //filtros:
        $tiposcintas_filter = TipoCinta::all();

        //filters:
        $query = TipoCinta::query();
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
        $orderby = in_array($orderby, ['id']) ? $orderby : 'descripcion';
        $sorted  = in_array($sorted, ['ASC', 'DESC']) ? $sorted : 'ASC';
        $tiposcintas = $query->orderBy($orderby, $sorted)->paginate(20);

        return view('tiposcintas.index', compact('tiposcintas', 'tiposcintas_filter'));
    }
    public function create()
    {

        return view('tiposcintas.create');
    }
    public function store(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'descripcion' => 'required',
            'codigo' => 'required|unique:tipos_cintas,codigo',
        ]);
        $tipocinta = new TipoCinta();
        $tipocinta->descripcion             = (trim($request->input('descripcion')) != '') ? $request->input('descripcion') : $tipocinta->descripcion;
        $tipocinta->codigo             = (trim($request->input('codigo')) != '') ? $request->input('codigo') : $tipocinta->descripcion;
        $tipocinta->save();
        return redirect()->route('mantenedores.tipos-cintas.list')->with('success', 'TipoCinta creado correctamente.');
    }
    public function edit($id)
    {
        $tipocinta = TipoCinta::find($id);

        return view('tiposcintas.edit', compact('tipocinta'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'descripcion' => 'required',
            'codigo' => 'required|unique:tipos_cintas,codigo,' .$id,
        ]);

        $tipocinta = TipoCinta::find($id);
        $tipocinta->descripcion             = (trim($request->input('descripcion')));
        $tipocinta->codigo             = (trim($request->input('codigo')));
        $tipocinta->save();
        return redirect()->route('mantenedores.tipos-cintas.list')->with('success', 'Tipo Cinta editado correctamente.');
    }

    public function active($id)
    {
        TipoCinta::findOrFail($id)->update(['active' => 1]);
        return redirect()->route('mantenedores.tipos-cintas.list')->with('success', 'Tipo Cinta activado correctamente.');
    }

    public function inactive($id)
    {
        TipoCinta::findOrFail($id)->update(['active' => 0]);
        return redirect()->route('mantenedores.tipos-cintas.list')->with('success', 'Tipo Cinta inactivado correctamente.');
    }
}
