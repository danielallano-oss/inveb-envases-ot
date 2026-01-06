<?php

namespace App\Http\Controllers;

use App\CeBe;
use App\Hierarchy;
use App\Mercado;
use App\Planta;
use Illuminate\Http\Request;

class CeBeController extends Controller
{
    public function index()
    {
        //filtros:
        $cebes_filter = CeBe::all();

        $planta_filter = Planta::all();
        $mercado_filter = Hierarchy::all();

        //filters:
        $query = CeBe::query();
        if (!is_null(request()->query('id'))) {
            $query = $query->whereIn('id', request()->query('id'));
        }
        if (!is_null(request()->query('planta_id'))) {
            $query = $query->whereIn('planta_id', request()->query('planta_id'));
        }

        if (!is_null(request()->query('hierearchie_id'))) {
            $query = $query->whereIn('hierearchie_id', request()->query('hierearchie_id'));
        }
        if (!is_null(request()->query('active'))) {
            $query = $query->whereIn('active', request()->query('active'));
        }
        /*order columnns table*/
        $orderby = trim(request()->query('orderby'));
        $sorted  = trim(request()->query('sorted'));
        $orderby = in_array($orderby, ['cebe']) ? $orderby : 'cebe';
        $sorted  = in_array($sorted, ['ASC', 'DESC']) ? $sorted : 'ASC';
        $cebes = $query->orderBy($orderby, $sorted)->paginate(20);

        return view('cebes.index', compact('cebes', 'cebes_filter','planta_filter','mercado_filter'));
    }
    public function create()
    {
        $plantas = Planta::pluck('nombre','id')->toArray();
        $mercados = Hierarchy::pluck('descripcion','id')->toArray();

        return view('cebes.create',compact('plantas','mercados'));
    }
    public function store(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'planta_id' => 'required',
            'tipo' => 'required',
            // 'hierearchie_id' => 'required',
            'cebe' => 'required',
            'nombre_cebe' => 'required',
            'grupo_gastos_generales' => 'required',
        ]);

        $cebe = new CeBe();
        $cebe->planta_id             = (trim($request->input('planta_id')) != '') ? $request->input('planta_id') : $cebe->planta_id;
        $cebe->tipo             = (trim($request->input('tipo')) != '') ? $request->input('tipo') : $cebe->tipo;
        $cebe->cebe             = (trim($request->input('cebe')) != '') ? $request->input('cebe') : $cebe->cebe;
        $cebe->nombre_cebe             = (trim($request->input('nombre_cebe')) != '') ? $request->input('nombre_cebe') : $cebe->nombre_cebe;
        $cebe->grupo_gastos_generales             = (trim($request->input('grupo_gastos_generales')) != '') ? $request->input('grupo_gastos_generales') : $cebe->grupo_gastos_generales;
        $cebe->hierearchie_id             = (trim($request->input('hierearchie_id')) != '') ? $request->input('hierearchie_id') : $cebe->hierearchie_id;


        $cebe->save();
        return redirect()->route('mantenedores.cebes.list')->with('success', 'CeBe creado correctamente.');
    }
    public function edit($id)
    {
        $cebe = CeBe::find($id);

        $plantas = Planta::pluck('nombre','id')->toArray();
        $mercados = Hierarchy::pluck('descripcion','id')->toArray();

        return view('cebes.edit', compact('cebe','plantas','mercados'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'planta_id' => 'required',
            'tipo' => 'required',
            // 'hierearchie_id_id' => 'required',
            'cebe' => 'required',
            'nombre_cebe' => 'required',
            'grupo_gastos_generales' => 'required',
        ]);

        $cebe = CeBe::find($id);
        $cebe->planta_id             = (trim($request->input('planta_id')));
        $cebe->tipo              = (trim($request->input('tipo')));
        $cebe->cebe              = (trim($request->input('cebe')));
        $cebe->nombre_cebe              = (trim($request->input('nombre_cebe')));
        $cebe->grupo_gastos_generales              = (trim($request->input('grupo_gastos_generales')));
        $cebe->hierearchie_id              = (trim($request->input('hierearchie_id')) != '' ? trim($request->input('hierearchie_id')) : null);
        $cebe->save();
        return redirect()->route('mantenedores.cebes.list')->with('success', 'CeBe editado correctamente.');
    }

    public function active($id)
    {
        CeBe::findOrFail($id)->update(['active' => 1]);
        return redirect()->route('mantenedores.cebes.list')->with('success', 'CeBe activado correctamente.');
    }

    public function inactive($id)
    {
        CeBe::findOrFail($id)->update(['active' => 0]);
        return redirect()->route('mantenedores.cebes.list')->with('success', 'CeBe inactivado correctamente.');
    }
}
