<?php

namespace App\Http\Controllers;

use App\Process;
use App\TiempoTratamiento;
use Illuminate\Http\Request;

class TiempoTratamientoController extends Controller
{
    public function index()
    {
        //filtros:
        $tiempotratamiento_filter = TiempoTratamiento::all();
        $procesos_filter = Process::all();

        //filters:
        $query = TiempoTratamiento::query();
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
        $tiempotratamiento = $query->with('proceso')->orderBy($orderby, $sorted)->paginate(20);

        return view('tiempotratamiento.index', compact('tiempotratamiento', 'tiempotratamiento_filter','procesos_filter'));
    }
    public function create()
    {
        $procesos = Process::pluck('descripcion','id', 'type')->toArray();

        return view('tiempotratamiento.create',compact('procesos'));
    }
    public function store(Request $request)
    {

        // dd(request()->all());
        $request->validate([
            'tiempo_buin' => 'required',
            'tiempo_tiltil' => 'required',
            'tiempo_osorno' => 'required',
            'tiempo_buin_powerply' => 'required',
            'tiempo_buin_cc_doble' => 'required',
            'proceso_id' => 'required|unique:tiempo_tratamiento,proceso_id',
        ],[
            'proceso_id.unique' => 'El proceso ya existe y debe ser único.'
        ]);
        $tiempotratamiento = new tiempotratamiento();
        $tiempotratamiento->tiempo_buin             = (trim($request->input('tiempo_buin')) != '') ? $request->input('tiempo_buin') : $tiempotratamiento->tiempo_buin;
        $tiempotratamiento->tiempo_tiltil             = (trim($request->input('tiempo_tiltil')) != '') ? $request->input('tiempo_tiltil') : $tiempotratamiento->tiempo_tiltil;
        $tiempotratamiento->tiempo_osorno             = (trim($request->input('tiempo_osorno')) != '') ? $request->input('tiempo_osorno') : $tiempotratamiento->tiempo_osorno;
        $tiempotratamiento->tiempo_buin_powerply             = (trim($request->input('tiempo_buin_powerply')) != '') ? $request->input('tiempo_buin_powerply') : $tiempotratamiento->tiempo_buin_powerply;
        $tiempotratamiento->tiempo_buin_cc_doble             = (trim($request->input('tiempo_buin_cc_doble')) != '') ? $request->input('tiempo_buin_cc_doble') : $tiempotratamiento->tiempo_buin_cc_doble;
        $tiempotratamiento->proceso_id             = (trim($request->input('proceso_id')) != '') ? $request->input('proceso_id') : $tiempotratamiento->proceso_id;
        $tiempotratamiento->save();
        return redirect()->route('mantenedores.tiempo-tratamiento.list')->with('success', 'Tiempo Tratamiento creado correctamente.');
    }
    public function edit($id)
    {
        $tiempotratamiento = TiempoTratamiento::find($id);
        $procesos = Process::pluck('descripcion','id', 'type')->toArray();

        return view('tiempotratamiento.edit', compact('tiempotratamiento','procesos'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'tiempo_buin' => 'required',
            'tiempo_tiltil' => 'required',
            'tiempo_osorno' => 'required',
            'tiempo_buin_powerply' => 'required',
            'tiempo_buin_cc_doble' => 'required',
            'proceso_id' => 'required|unique:tiempo_tratamiento,proceso_id,' .$id,
        ]);

        $tiempotratamiento = TiempoTratamiento::find($id);
        $tiempotratamiento->tiempo_buin             = (trim($request->input('tiempo_buin')));
        $tiempotratamiento->tiempo_tiltil              = (trim($request->input('tiempo_tiltil')));
        $tiempotratamiento->tiempo_osorno              = (trim($request->input('tiempo_osorno')));
        $tiempotratamiento->tiempo_buin_powerply              = (trim($request->input('tiempo_buin_powerply')));
        $tiempotratamiento->tiempo_buin_cc_doble              = (trim($request->input('tiempo_buin_cc_doble')));
        $tiempotratamiento->proceso_id              = (trim($request->input('proceso_id')));
        $tiempotratamiento->save();
        return redirect()->route('mantenedores.tiempo-tratamiento.list')->with('success', 'Tiempo Tratamiento editado correctamente.');
    }

    public function active($id)
    {
        TiempoTratamiento::findOrFail($id)->update(['active' => 1]);
        return redirect()->route('mantenedores.tiempo-tratamiento.list')->with('success', 'Tiempo Tratamiento activado correctamente.');
    }

    public function inactive($id)
    {
        TiempoTratamiento::findOrFail($id)->update(['active' => 0]);
        return redirect()->route('mantenedores.tiempo-tratamiento.list')->with('success', 'Tiempo Tratamiento inactivado correctamente.');
    }
}
