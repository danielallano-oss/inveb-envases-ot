<?php

namespace App\Http\Controllers;

use App\GrupoPlanta;
use App\Planta;
use Illuminate\Http\Request;

class GrupoPlantasController extends Controller
{
    public function index()
    {
        //filtros:

        $grupoplantas_filter = GrupoPlanta::select('grupo_plantas.*', 'plantas.nombre as nombre_planta')
        ->join('plantas', 'grupo_plantas.planta_id', '=', 'plantas.id')
        ->get();
        $planta_filter = Planta::all();

        //filters:
        $query = GrupoPlanta::query();
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
        $orderby = in_array($orderby, ['id']) ? $orderby : 'id';
        $sorted  = in_array($sorted, ['ASC', 'DESC']) ? $sorted : 'ASC';
        $grupoplantas = $query->with('planta')->orderBy($orderby, $sorted)->paginate(20);

        return view('grupoplantas.index', compact('grupoplantas', 'grupoplantas_filter','planta_filter'));
    }
    public function create()
    {
        $plantas = Planta::pluck('nombre','id')->toArray();

        return view('grupoplantas.create',compact('plantas'));
    }
    public function store(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'centro' => 'required',
            'num_almacen' => 'required',
            // 'cebe' => 'required',
            'planta_id' => 'required|unique:grupo_plantas,planta_id',
        ]);
        $grupoplanta = new GrupoPlanta();
        $grupoplanta->centro             = (trim($request->input('centro')) != '') ? $request->input('centro') : $grupoplanta->centro;
        $grupoplanta->num_almacen             = (trim($request->input('num_almacen')) != '') ? $request->input('num_almacen') : $grupoplanta->num_almacen;
        // $grupoplanta->cebe             = (trim($request->input('cebe')) != '') ? $request->input('cebe') : $grupoplanta->cebe;
        $grupoplanta->planta_id             = (trim($request->input('planta_id')) != '') ? $request->input('planta_id') : $grupoplanta->planta_id;
        $grupoplanta->save();
        return redirect()->route('mantenedores.grupo-plantas.list')->with('success', 'Grupo Planta creado correctamente.');
    }
    public function edit($id)
    {
        $grupoplanta = GrupoPlanta::find($id);
        $plantas = Planta::pluck('nombre','id')->toArray();

        return view('grupoplantas.edit', compact('grupoplanta','plantas'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'centro' => 'required',
            'num_almacen' => 'required',
            // 'cebe' => 'required',
            'planta_id' => 'required|unique:grupo_plantas,planta_id,' .$id,
        ]);

        $grupoplanta = GrupoPlanta::find($id);
        $grupoplanta->centro             = (trim($request->input('centro')));
        $grupoplanta->num_almacen             = (trim($request->input('num_almacen')));
        // $grupoplanta->cebe             = (trim($request->input('cebe')));
        $grupoplanta->planta_id              = (trim($request->input('planta_id')));
        $grupoplanta->save();

        return redirect()->route('mantenedores.grupo-plantas.list')->with('success', 'grupo planta editado correctamente.');
    }

    public function active($id)
    {
        GrupoPlanta::findOrFail($id)->update(['active' => 1]);
        return redirect()->route('mantenedores.grupo-plantas.list')->with('success', 'grupo planta activado correctamente.');
    }

    public function inactive($id)
    {
        GrupoPlanta::findOrFail($id)->update(['active' => 0]);
        return redirect()->route('mantenedores.grupo-plantas.list')->with('success', 'grupo planta inactivado correctamente.');
    }
}
