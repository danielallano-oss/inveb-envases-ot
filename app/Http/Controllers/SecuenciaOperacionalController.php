<?php

namespace App\Http\Controllers;

use App\Planta;
use App\SecuenciaOperacional;
use Illuminate\Http\Request;

class SecuenciaOperacionalController extends Controller
{
    public function index()
    {
        //filtros:
        $secuenciasoperacionales_filter = SecuenciaOperacional::all();
        $plantas_filter = Planta::all();

        //filters:
        $query = SecuenciaOperacional::query();
        if (!is_null(request()->query('id'))) {
            $query = $query->whereIn('id', request()->query('id'));
        }

        if (!is_null(request()->query('planta_id'))) {
            $query = $query->whereIn('planta_id', request()->query('planta_id'));
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
        $orderby = in_array($orderby, ['codigo', 'descripcion']) ? $orderby : 'descripcion';
        $sorted  = in_array($sorted, ['ASC', 'DESC']) ? $sorted : 'ASC';
        $secuenciasoperacionales = $query->with('planta')->orderBy($orderby, $sorted)->paginate(20);

        return view('secuenciasoperacionales.index', compact('secuenciasoperacionales', 'secuenciasoperacionales_filter','plantas_filter'));
    }
    public function create()
    {
        $plantas = Planta::pluck('nombre','id')->toArray();

        return view('secuenciasoperacionales.create',compact('plantas'));
    }
    public function store(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'codigo' => 'required|unique:secuencias_operacionales,codigo',
            'descripcion' => 'required',
            'planta_id' => 'required',
        ]);
        $secuenciaoperacional = new SecuenciaOperacional();
        $secuenciaoperacional->codigo       = (trim($request->input('codigo')) != '') ? $request->input('codigo') : $secuenciaoperacional->codigo;
        $secuenciaoperacional->descripcion  = (trim($request->input('descripcion')) != '') ? $request->input('descripcion') : $secuenciaoperacional->descripcion;
        $secuenciaoperacional->nombre_corto = (trim($request->input('nombre_corto')) != '') ? $request->input('nombre_corto') : $secuenciaoperacional->nombre_corto;
        $secuenciaoperacional->planta_id    = (trim($request->input('planta_id')) != '') ? $request->input('planta_id') : $secuenciaoperacional->planta_id;
        $secuenciaoperacional->save();
        return redirect()->route('mantenedores.secuencias-operacionales.list')->with('success', 'Secuencia Operacional creada correctamente.');
    }
    public function edit($id)
    {
        $secuenciaoperacional = SecuenciaOperacional::find($id);
        $plantas = Planta::pluck('nombre','id')->toArray();

        return view('secuenciasoperacionales.edit', compact('secuenciaoperacional','plantas'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'codigo' => 'required|unique:secuencias_operacionales,codigo,' . $id,
            'descripcion' => 'required',
            'planta_id' => 'required',
        ]);

        $secuenciaoperacional = SecuenciaOperacional::find($id);
        $secuenciaoperacional->codigo       = (trim($request->input('codigo')) != '') ? $request->input('codigo') : $secuenciaoperacional->codigo;
        $secuenciaoperacional->descripcion  = (trim($request->input('descripcion')) != '') ? $request->input('descripcion') : $secuenciaoperacional->descripcion;
        $secuenciaoperacional->nombre_corto = (trim($request->input('nombre_corto')) != '') ? $request->input('nombre_corto') : $secuenciaoperacional->nombre_corto;
        $secuenciaoperacional->planta_id    = (trim($request->input('planta_id')) != '') ? $request->input('planta_id') : $secuenciaoperacional->planta_id;
        $secuenciaoperacional->save();
        return redirect()->route('mantenedores.secuencias-operacionales.list')->with('success', 'Secuencia Operacional editada correctamente.');
    }

    public function active($id)
    {
        SecuenciaOperacional::findOrFail($id)->update(['active' => 1]);
        return redirect()->route('mantenedores.secuencias-operacionales.list')->with('success', 'Secuencia Operacional activada correctamente.');
    }

    public function inactive($id)
    {
        SecuenciaOperacional::findOrFail($id)->update(['active' => 0]);
        return redirect()->route('mantenedores.secuencias-operacionales.list')->with('success', 'Secuencia Operacional inactivada correctamente.');
    }
}
