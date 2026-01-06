<?php

namespace App\Http\Controllers;

use App\CantidadBase;
use App\Process;
use Illuminate\Http\Request;

class CantidadBaseController extends Controller
{
    public function index()
    {
        //filtros:
        $cantidadbase_filter = CantidadBase::all();
        $procesos_filter = Process::all();

        //filters:
        $query = CantidadBase::query();
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
        $cantidadbase = $query->with('proceso')->orderBy($orderby, $sorted)->paginate(20);

        return view('cantidadbase.index', compact('cantidadbase', 'cantidadbase_filter','procesos_filter'));
    }
    public function create()
    {
        $procesos = Process::pluck('descripcion','id', 'type')->toArray();

        return view('cantidadbase.create',compact('procesos'));
    }
    public function store(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'cantidad_buin' => 'required',
            'cantidad_tiltil' => 'required',
            'cantidad_osorno' => 'required',
            'proceso_id' => 'required|unique:cantidad_base,proceso_id',
        ],[
            'proceso_id.unique' => 'El proceso ya existe y debe ser único.'
        ]);
        $cantidadbase = new CantidadBase();
        $cantidadbase->cantidad_buin             = (trim($request->input('cantidad_buin')) != '') ? $request->input('cantidad_buin') : $cantidadbase->cantidad_buin;
        $cantidadbase->cantidad_tiltil             = (trim($request->input('cantidad_tiltil')) != '') ? $request->input('cantidad_tiltil') : $cantidadbase->cantidad_tiltil;
        $cantidadbase->cantidad_osorno             = (trim($request->input('cantidad_osorno')) != '') ? $request->input('cantidad_osorno') : $cantidadbase->cantidad_osorno;
        $cantidadbase->proceso_id             = (trim($request->input('proceso_id')) != '') ? $request->input('proceso_id') : $cantidadbase->proceso_id;
        $cantidadbase->save();
        return redirect()->route('mantenedores.cantidad-base.list')->with('success', 'Cantidad Base creado correctamente.');
    }
    public function edit($id)
    {
        $cantidadbase = CantidadBase::find($id);
        $procesos = Process::pluck('descripcion','id', 'type')->toArray();

        return view('cantidadbase.edit', compact('cantidadbase','procesos'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'cantidad_buin' => 'required',
            'cantidad_tiltil' => 'required',
            'cantidad_osorno' => 'required',
            'proceso_id' => 'required|unique:cantidad_base,proceso_id,' .$id,
        ]);

        $cantidadbase = CantidadBase::find($id);
        $cantidadbase->cantidad_buin             = (trim($request->input('cantidad_buin')));
        $cantidadbase->cantidad_tiltil              = (trim($request->input('cantidad_tiltil')));
        $cantidadbase->cantidad_osorno              = (trim($request->input('cantidad_osorno')));
        $cantidadbase->proceso_id              = (trim($request->input('proceso_id')));
        $cantidadbase->save();
        return redirect()->route('mantenedores.cantidad-base.list')->with('success', 'CantidadBase editado correctamente.');
    }

    public function active($id)
    {
        CantidadBase::findOrFail($id)->update(['active' => 1]);
        return redirect()->route('mantenedores.cantidad-base.list')->with('success', 'CantidadBase activado correctamente.');
    }

    public function inactive($id)
    {
        CantidadBase::findOrFail($id)->update(['active' => 0]);
        return redirect()->route('mantenedores.cantidad-base.list')->with('success', 'CantidadBase inactivado correctamente.');
    }
}
