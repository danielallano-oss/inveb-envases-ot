<?php

namespace App\Http\Controllers;

use App\GrupoImputacionMaterial;
use App\Process;
use Illuminate\Http\Request;

class GrupoImputacionMaterialController extends Controller
{
    public function index()
    {
        //filtros:
        $grupoimputacionmaterial_filter = GrupoImputacionMaterial::all();


        $procesos_filter = (object)[
            (object)['descripcion' => 'FLEXO'],
            (object)['descripcion' => 'DIECUTTER'],
            (object)['descripcion' => 'S/PROCESO'],
            (object)['descripcion' => 'DIECUTTER-C/PEGADO'],
            (object)['descripcion' => 'FLEXO/MATRIZ PARCIAL'],
            (object)['descripcion' => 'DIECUTTER-C/PROCESO'],
            (object)['descripcion' => 'OFFSET'],
            (object)['descripcion' => 'FLEXO-C/TROQUELADO'],
            (object)['descripcion' => 'OFFSET-C/PEGADO'],
            (object)['descripcion' => 'FLEXO/MATRIZ COMPLET'],
            (object)['descripcion' => 'DIECUTTER - ALTA GRAFICA'],
            (object)['descripcion' => 'DIECUTTER -C/PEGADO ALTA GRAFICA'],
            (object)['descripcion' => 'CORRUGADO'],
            (object)['descripcion' => 'ESQUINEROS'],
            (object)['descripcion' => 'PIEZAS INTERIORES'],
            (object)['descripcion' => 'SEMICORRUGADO'],
            (object)['descripcion' => 'SEMIP. INTERIOR'],
        ];


        // $procesos_filter = Process::all();


        //filters:
        $query = GrupoImputacionMaterial::query();
        if (!is_null(request()->query('id'))) {
            $query = $query->whereIn('id', request()->query('id'));
        }

        if (!is_null(request()->query('proceso'))) {
            $query = $query->whereIn('proceso', request()->query('proceso'));
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
        $orderby = in_array($orderby, ['codigo']) ? $orderby : 'codigo';
        $sorted  = in_array($sorted, ['ASC', 'DESC']) ? $sorted : 'ASC';
        $grupoimputacionmateriales = $query->orderBy($orderby, $sorted)->paginate(20);

        return view('grupoimputacionmateriales.index', compact('grupoimputacionmateriales', 'grupoimputacionmaterial_filter', 'procesos_filter'));
    }
    public function create()
    {
        // $procesos = Process::pluck('descripcion', 'id', 'type')->toArray();
        $procesos_array = [
            (object)['descripcion' => 'FLEXO'],
            (object)['descripcion' => 'DIECUTTER'],
            (object)['descripcion' => 'S/PROCESO'],
            (object)['descripcion' => 'DIECUTTER-C/PEGADO'],
            (object)['descripcion' => 'FLEXO/MATRIZ PARCIAL'],
            (object)['descripcion' => 'DIECUTTER-C/PROCESO'],
            (object)['descripcion' => 'OFFSET'],
            (object)['descripcion' => 'FLEXO-C/TROQUELADO'],
            (object)['descripcion' => 'OFFSET-C/PEGADO'],
            (object)['descripcion' => 'FLEXO/MATRIZ COMPLET'],
            (object)['descripcion' => 'DIECUTTER - ALTA GRAFICA'],
            (object)['descripcion' => 'DIECUTTER -C/PEGADO ALTA GRAFICA'],
            (object)['descripcion' => 'CORRUGADO'],
            (object)['descripcion' => 'ESQUINEROS'],
            (object)['descripcion' => 'PIEZAS INTERIORES'],
            (object)['descripcion' => 'SEMICORRUGADO'],
            (object)['descripcion' => 'SEMIP. INTERIOR'],
        ];

        $procesos_desc = array_map(function($proceso) {
            return $proceso->descripcion;
        }, $procesos_array);

        $procesos = array_combine(
            array_map('strtoupper', $procesos_desc),
            $procesos_desc
        );


        return view('grupoimputacionmateriales.create', compact('procesos'));
    }
    public function store(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'codigo' => 'required',
            'familia' => 'required',
            'material_modelo' => 'required',
            'proceso' => 'required|unique:grupo_imputacion_materiales,proceso',
        ], [
            'proceso_id.unique' => 'El proceso ya existe y debe ser único.'
        ]);
        $grupoimputacionmaterial = new GrupoImputacionMaterial();
        $grupoimputacionmaterial->codigo             = (trim($request->input('codigo')) != '') ? $request->input('codigo') : $grupoimputacionmaterial->codigo;
        $grupoimputacionmaterial->familia             = (trim($request->input('familia')) != '') ? $request->input('familia') : $grupoimputacionmaterial->familia;
        $grupoimputacionmaterial->material_modelo             = (trim($request->input('material_modelo')) != '') ? $request->input('familia') : $grupoimputacionmaterial->material_modelo;
        $grupoimputacionmaterial->proceso             = (trim($request->input('proceso')) != '') ? $request->input('proceso') : $grupoimputacionmaterial->proceso;
        $grupoimputacionmaterial->save();
        return redirect()->route('mantenedores.grupo-imputacion-material.list')->with('success', 'Grupo imputacion material creado correctamente.');
    }
    public function edit($id)
    {
        $grupoimputacionmaterial = GrupoImputacionMaterial::find($id);

        $procesos_array = [
            (object)['descripcion' => 'FLEXO'],
            (object)['descripcion' => 'DIECUTTER'],
            (object)['descripcion' => 'S/PROCESO'],
            (object)['descripcion' => 'DIECUTTER-C/PEGADO'],
            (object)['descripcion' => 'FLEXO/MATRIZ PARCIAL'],
            (object)['descripcion' => 'DIECUTTER-C/PROCESO'],
            (object)['descripcion' => 'OFFSET'],
            (object)['descripcion' => 'FLEXO-C/TROQUELADO'],
            (object)['descripcion' => 'OFFSET-C/PEGADO'],
            (object)['descripcion' => 'FLEXO/MATRIZ COMPLET'],
            (object)['descripcion' => 'DIECUTTER - ALTA GRAFICA'],
            (object)['descripcion' => 'DIECUTTER -C/PEGADO ALTA GRAFICA'],
            (object)['descripcion' => 'CORRUGADO'],
            (object)['descripcion' => 'ESQUINEROS'],
            (object)['descripcion' => 'PIEZAS INTERIORES'],
            (object)['descripcion' => 'SEMICORRUGADO'],
            (object)['descripcion' => 'SEMIP. INTERIOR'],
        ];

        $procesos_desc = array_map(function($proceso) {
            return $proceso->descripcion;
        }, $procesos_array);

        $procesos = array_combine(
            array_map('strtoupper', $procesos_desc),
            $procesos_desc
        );

        // $procesos = Process::pluck('descripcion', 'id', 'type')->toArray();

        return view('grupoimputacionmateriales.edit', compact('grupoimputacionmaterial', 'procesos'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'codigo' => 'required',
            'familia' => 'required',
            'material_modelo' => 'required',
            'proceso' => 'required|unique:grupo_imputacion_materiales,proceso,' . $id,
        ]);

        $grupoimputacionmaterial = GrupoImputacionMaterial::find($id);
        $grupoimputacionmaterial->codigo             = (trim($request->input('codigo')));
        $grupoimputacionmaterial->familia             = (trim($request->input('familia')));
        $grupoimputacionmaterial->material_modelo             = (trim($request->input('material_modelo')));
        $grupoimputacionmaterial->proceso              = (trim($request->input('proceso')));
        $grupoimputacionmaterial->save();
        return redirect()->route('mantenedores.grupo-imputacion-material.list')->with('success', 'grupo imputacion material editado correctamente.');
    }

    public function active($id)
    {
        GrupoImputacionMaterial::findOrFail($id)->update(['active' => 1]);
        return redirect()->route('mantenedores.grupo-imputacion-material.list')->with('success', 'grupo imputacion material activado correctamente.');
    }

    public function inactive($id)
    {
        GrupoImputacionMaterial::findOrFail($id)->update(['active' => 0]);
        return redirect()->route('mantenedores.grupo-imputacion-material.list')->with('success', 'grupo imputacion material inactivado correctamente.');
    }
}
