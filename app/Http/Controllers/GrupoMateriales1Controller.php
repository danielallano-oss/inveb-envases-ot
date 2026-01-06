<?php

namespace App\Http\Controllers;

use App\Armado;
use App\GrupoMateriales1;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GrupoMateriales1Controller extends Controller
{
    public function index()
    {
        //filtros:
        $grupomateriales_filter = GrupoMateriales1::select('grupo_materiales_1.*', 'armados.descripcion as armado_desc')
            ->join('armados', 'grupo_materiales_1.armado_id', '=', 'armados.id')
            ->get();
        $armado_filter = Armado::where('active',1)->get();

        //filters:
        $query = GrupoMateriales1::query();
        if (!is_null(request()->query('id'))) {
            $query = $query->whereIn('id', request()->query('id'));
        }

        if (!is_null(request()->query('armado_id'))) {
            $query = $query->whereIn('armado_id', request()->query('armado_id'));
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
        $grupomateriales = $query->with('armado')->orderBy($orderby, $sorted)->paginate(20);

        return view('grupomateriales1.index', compact('grupomateriales', 'grupomateriales_filter', 'armado_filter'));
    }
    public function create()
    {
        $armados = Armado::where('active',1)->pluck('descripcion', 'id')->toArray();

        return view('grupomateriales1.create', compact('armados'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'codigo' => [
                'required',
                Rule::unique('grupo_materiales_1')->where(function ($query) use ($request) {
                    return $query->where('armado_id', $request->input('armado_id'));
                })
            ],
            'armado_id' => 'required',
        ]);

        $grupomaterial = new GrupoMateriales1();
        $grupomaterial->codigo             = (trim($request->input('codigo')) != '') ? $request->input('codigo') : $grupomaterial->codigo;
        $grupomaterial->armado_id             = (trim($request->input('armado_id')) != '') ? $request->input('armado_id') : $grupomaterial->armado_id;
        $grupomaterial->save();
        return redirect()->route('mantenedores.grupo-materiales-1.list')->with('success', 'Grupo Material creado correctamente.');
    }


    public function edit($id)
    {
        $grupomaterial = GrupoMateriales1::find($id);
        $armados = Armado::where('active',1)->pluck('descripcion', 'id')->toArray();

        return view('grupomateriales1.edit', compact('grupomaterial', 'armados'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'codigo' => [
                'required',
                Rule::unique('grupo_materiales_1')->where(function ($query) use ($request, $id) {
                    return $query->where('armado_id', $request->input('armado_id'))
                        ->where('id', '!=', $id);
                }),
            ],
            'armado_id' => 'required',
        ]);

        $grupomaterial = GrupoMateriales1::find($id);
        $grupomaterial->codigo             = (trim($request->input('codigo')));
        $grupomaterial->armado_id              = (trim($request->input('armado_id')));
        $grupomaterial->save();

        return redirect()->route('mantenedores.grupo-materiales-1.list')->with('success', 'grupo material editado correctamente.');
    }

    public function active($id)
    {
        GrupoMateriales1::findOrFail($id)->update(['active' => 1]);
        return redirect()->route('mantenedores.grupo-materiales-1.list')->with('success', 'grupo material activado correctamente.');
    }

    public function inactive($id)
    {
        GrupoMateriales1::findOrFail($id)->update(['active' => 0]);
        return redirect()->route('mantenedores.grupo-materiales-1.list')->with('success', 'grupo material inactivado correctamente.');
    }
}
