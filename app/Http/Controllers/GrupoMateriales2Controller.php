<?php

namespace App\Http\Controllers;

use App\GrupoMateriales2;
use App\ProductType;
use Illuminate\Http\Request;

class GrupoMateriales2Controller extends Controller
{
    public function index()
    {
        //filtros:
        $grupomateriales_filter = GrupoMateriales2::all();
        $producttype_filter = ProductType::all();

        //filters:
        $query = GrupoMateriales2::query();
        if (!is_null(request()->query('id'))) {
            $query = $query->whereIn('id', request()->query('id'));
        }

        if (!is_null(request()->query('pruduct_type_id'))) {
            $query = $query->whereIn('pruduct_type_id', request()->query('pruduct_type_id'));
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
        $grupomateriales = $query->with('tipo_producto')->orderBy($orderby, $sorted)->paginate(20);

        return view('grupomateriales2.index', compact('grupomateriales', 'grupomateriales_filter','producttype_filter'));
    }
    public function create()
    {
        $producttypes = ProductType::pluck('descripcion','id','codigo')->toArray();

        return view('grupomateriales2.create',compact('producttypes'));
    }
    public function store(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'codigo' => 'required|unique:grupo_materiales_1,codigo',
            'pruduct_type_id' => 'required|unique:grupo_materiales_2,pruduct_type_id',
        ],[
            'pruduct_type_id.unique' => 'El proceso ya existe y debe ser único.'
        ]);
        $grupomaterial = new GrupoMateriales2();
        $grupomaterial->codigo             = (trim($request->input('codigo')) != '') ? $request->input('codigo') : $grupomaterial->codigo;
        $grupomaterial->pruduct_type_id             = (trim($request->input('pruduct_type_id')) != '') ? $request->input('pruduct_type_id') : $grupomaterial->pruduct_type_id;
        $grupomaterial->save();
        return redirect()->route('mantenedores.grupo-materiales-2.list')->with('success', 'Grupo Material creado correctamente.');
    }
    public function edit($id)
    {
        $grupomaterial = GrupoMateriales2::find($id);
        $producttypes = ProductType::pluck('descripcion','id')->toArray();

        return view('grupomateriales2.edit', compact('grupomaterial','producttypes'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'codigo' => 'required',
            'pruduct_type_id' => 'required|unique:grupo_materiales_2,pruduct_type_id,' .$id,
        ]);

        $grupomaterial = GrupoMateriales2::find($id);
        $grupomaterial->codigo             = (trim($request->input('codigo')));
        $grupomaterial->pruduct_type_id              = (trim($request->input('pruduct_type_id')));
        $grupomaterial->save();

        return redirect()->route('mantenedores.grupo-materiales-2.list')->with('success', 'grupo material editado correctamente.');
    }

    public function active($id)
    {
        GrupoMateriales2::findOrFail($id)->update(['active' => 1]);
        return redirect()->route('mantenedores.grupo-materiales-2.list')->with('success', 'grupo material activado correctamente.');
    }

    public function inactive($id)
    {
        GrupoMateriales2::findOrFail($id)->update(['active' => 0]);
        return redirect()->route('mantenedores.grupo-materiales-2.list')->with('success', 'grupo material inactivado correctamente.');
    }
}
