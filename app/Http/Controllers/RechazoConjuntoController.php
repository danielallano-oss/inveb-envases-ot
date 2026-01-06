<?php

namespace App\Http\Controllers;

use App\Process;
use App\RechazoConjunto;
use Illuminate\Http\Request;

class RechazoConjuntoController extends Controller
{
    public function index()
    {
        //filtros:
        $rechazoconjunto_filter = RechazoConjunto::all();
        $procesos_filter = Process::all();

        //filters:
        $query = RechazoConjunto::query();
        if (!is_null(request()->query('id'))) {
            $query = $query->whereIn('id', request()->query('id'));
        }

        if (!is_null(request()->query('proceso_id'))) {
            $query = $query->whereIn('proceso_id', request()->query('proceso_id'));
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
        $orderby = in_array($orderby, ['proceso_id']) ? $orderby : 'id';
        $sorted  = in_array($sorted, ['ASC', 'DESC']) ? $sorted : 'ASC';
        $rechazoconjunto = $query->with('proceso')->orderBy($orderby, $sorted)->paginate(20);

        return view('rechazoconjunto.index', compact('rechazoconjunto', 'rechazoconjunto_filter','procesos_filter'));
    }
    public function create()
    {
        $procesos = Process::pluck('descripcion','id', 'type')->toArray();

        return view('rechazoconjunto.create',compact('procesos'));
    }
    public function store(Request $request)
    {
        // dd(request()->all());

        $request->validate([
            'porcentaje_proceso_solo' => 'required',
            'porcentaje_proceso_barniz' => 'required',
            'porcentaje_proceso_maquila' => 'required',
            'codigo' => 'required',
            'proceso_id' => 'required|unique:rechazo_conjunto,proceso_id',
        ]);



        $rechazoconjunto = new rechazoconjunto();
        $rechazoconjunto->porcentaje_proceso_solo             = (trim($request->input('porcentaje_proceso_solo')) != '') ? $request->input('porcentaje_proceso_solo') : $rechazoconjunto->porcentaje_proceso_solo;
        $rechazoconjunto->porcentaje_proceso_barniz             = (trim($request->input('porcentaje_proceso_barniz')) != '') ? $request->input('porcentaje_proceso_barniz') : $rechazoconjunto->porcentaje_proceso_barniz;
        $rechazoconjunto->porcentaje_proceso_maquila             = (trim($request->input('porcentaje_proceso_maquila')) != '') ? $request->input('porcentaje_proceso_maquila') : $rechazoconjunto->porcentaje_proceso_maquila;
        $rechazoconjunto->codigo             = (trim($request->input('codigo')) != '') ? $request->input('codigo') : $rechazoconjunto->codigo;
        $rechazoconjunto->proceso_id             = (trim($request->input('proceso_id')) != '') ? $request->input('proceso_id') : $rechazoconjunto->proceso_id;
        $rechazoconjunto->save();

        return redirect()->route('mantenedores.rechazo-conjunto.list')->with('success', 'Rechazo Conjunto creado correctamente.');
    }
    public function edit($id)
    {
        $rechazoconjunto = RechazoConjunto::find($id);
        $procesos = Process::pluck('descripcion','id', 'type')->toArray();

        return view('rechazoconjunto.edit', compact('rechazoconjunto','procesos'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'porcentaje_proceso_solo' => 'required',
            'porcentaje_proceso_barniz' => 'required',
            'porcentaje_proceso_maquila' => 'required',
            'codigo' => 'required',
            // 'proceso_id' => 'required|unique:rechazo_conjunto,proceso_id,' .$id,
        ]);

        $rechazoconjunto = RechazoConjunto::find($id);
        $rechazoconjunto->porcentaje_proceso_solo             = (trim($request->input('porcentaje_proceso_solo')));
        $rechazoconjunto->porcentaje_proceso_barniz              = (trim($request->input('porcentaje_proceso_barniz')));
        $rechazoconjunto->porcentaje_proceso_maquila              = (trim($request->input('porcentaje_proceso_maquila')));
        $rechazoconjunto->codigo              = (trim($request->input('codigo')));
        // $rechazoconjunto->proceso_id              = (trim($request->input('proceso_id')));
        $rechazoconjunto->save();
        return redirect()->route('mantenedores.rechazo-conjunto.list')->with('success', 'rechazo conjunto editado correctamente.');
    }

    public function active($id)
    {
        RechazoConjunto::findOrFail($id)->update(['active' => 1]);
        return redirect()->route('mantenedores.rechazo-conjunto.list')->with('success', 'rechazo conjunto activado correctamente.');
    }

    public function inactive($id)
    {
        RechazoConjunto::findOrFail($id)->update(['active' => 0]);
        return redirect()->route('mantenedores.rechazo-conjunto.list')->with('success', 'rechazo conjunto inactivado correctamente.');
    }
}
