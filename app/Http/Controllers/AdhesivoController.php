<?php

namespace App\Http\Controllers;

use App\Adhesivo;
use App\Planta;
use Illuminate\Http\Request;

class AdhesivoController extends Controller
{
    public function index()
    {
        
        //filtros:
        $adhesivos_filter = Adhesivo::all();
        //filters:
        $query = Adhesivo::query();
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
        $orderby = in_array($orderby, ['planta_id']) ? $orderby : 'planta_id';
        $sorted  = in_array($sorted, ['ASC', 'DESC']) ? $sorted : 'ASC';
        $adhesivos = $query->orderBy($orderby, $sorted)->paginate(20);

        return view('adhesivos.index', compact('adhesivos', 'adhesivos_filter'));
    }
    
    public function create()
    {
        $plantas = Planta::pluck('nombre','id')->toArray();
       
        return view('adhesivos.create',compact('plantas'));
    }

    public function store(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'planta_id' => 'required',
            'maquina' => 'required',
            'codigo' => 'required',
            'consumo' => 'required',
        ]);
        $adhesivo = new Adhesivo();
        $adhesivo->planta_id   = (trim($request->input('planta_id')) != '') ? $request->input('planta_id') : $adhesivo->planta_id;
        $adhesivo->maquina   = (trim($request->input('maquina')) != '') ? $request->input('maquina') : $adhesivo->maquina;
        $adhesivo->codigo   = (trim($request->input('codigo')) != '') ? $request->input('codigo') : $adhesivo->codigo;
        $adhesivo->consumo   = (trim($request->input('consumo')) != '') ? $request->input('consumo') : $adhesivo->consumo;
        $adhesivo->save();
        return redirect()->route('mantenedores.adhesivos.list')->with('success', 'Adhesivo creado correctamente.');
    }

    public function edit($id)
    {
        $adhesivo = Adhesivo::find($id);
        $plantas = Planta::pluck('nombre','id')->toArray();
        return view('adhesivos.edit', compact('adhesivo','plantas'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'planta_id' => 'required',
            'maquina' => 'required',
            'codigo' => 'required',
            'consumo' => 'required',
        ]);

        $adhesivo = Adhesivo::find($id);
        $adhesivo->planta_id    = (trim($request->input('planta_id')) != '') ? $request->input('planta_id') : $adhesivo->planta_id;
        $adhesivo->maquina      = (trim($request->input('maquina')) != '') ? $request->input('maquina') : $adhesivo->maquina;
        $adhesivo->codigo       = (trim($request->input('codigo')) != '') ? $request->input('codigo') : $adhesivo->codigo;
        $adhesivo->consumo      = (trim($request->input('consumo')) != '') ? $request->input('consumo') : $adhesivo->consumo;
        $adhesivo->save();
        return redirect()->route('mantenedores.adhesivos.list')->with('success', 'Adhesivo editado correctamente.');
    }

    public function active($id)
    {
        Adhesivo::findOrFail($id)->update(['active' => 1]);
        return redirect()->route('mantenedores.adhesivos.list')->with('success', 'Adhesivo activado correctamente.');
    }

    public function inactive($id)
    {
        Adhesivo::findOrFail($id)->update(['active' => 0]);
        return redirect()->route('mantenedores.adhesivos.list')->with('success', 'Adhesivo inactivada correctamente.');
    }
}
