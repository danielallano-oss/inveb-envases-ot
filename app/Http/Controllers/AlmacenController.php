<?php

namespace App\Http\Controllers;

use App\Almacen;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class AlmacenController extends Controller
{
    public function index()
    {
        //filtros:
        $almacenes_filter = Almacen::all();

        //filters:
        $query = Almacen::query();
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
        $orderby = in_array($orderby, ['codigo', 'denominacion']) ? $orderby : 'denominacion';
        $sorted  = in_array($sorted, ['ASC', 'DESC']) ? $sorted : 'ASC';
        $almacenes = $query->orderBy($orderby, $sorted)->paginate(20);

        return view('almacenes.index', compact('almacenes', 'almacenes_filter'));
    }
    public function create()
    {

        return view('almacenes.create');
    }
    public function store(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'codigo' => [
                'required',
                Rule::unique('almacenes')->where(function ($query) use ($request) {
                    return $query->where('centro', $request->input('centro'));
                })
            ],
            'denominacion' => 'required',
            'centro' => 'required',
        ], [
            'codigo.unique' => 'La combinación de código y centro ya existe.'
        ]);

        // $search_almacen = Almacen::where('codigo',$request->input('codigo'))->where('centro', $request->input('centro'))->get()->first();
        // if($search_almacen){
        //     return redirect()->back()->with('error', 'Ya existe un registro con el mismo código y centro');
        // }else{

            $almacen = new Almacen();
            $almacen->codigo             = (trim($request->input('codigo')) != '') ? $request->input('codigo') : $almacen->codigo;
            $almacen->denominacion             = (trim($request->input('denominacion')) != '') ? $request->input('denominacion') : $almacen->denominacion;
            $almacen->centro             = (trim($request->input('centro')) != '') ? $request->input('centro') : $almacen->centro;
            $almacen->save();
            return redirect()->route('mantenedores.almacenes.list')->with('success', 'almacen creado correctamente.');
        // }
       
    }
    public function edit($id)
    {
        $almacen = Almacen::find($id);

        return view('almacenes.edit', compact('almacen'));
    }

    public function update(Request $request, $id)
    {
        

        // $request->validate([
        //     'codigo' => 'required|unique:almacenes,codigo,' . $id,
        //     'denominacion' => 'required',
        //     'centro' => 'required',
        // ]);
        $request->validate([
            'codigo' => [
                'required',
                Rule::unique('almacenes')->where(function ($query) use ($request, $id) {
                    return $query->where('centro', $request->input('centro'))
                                 ->where('id', '!=', $id);
                }),
            ],
            'armado_id' => 'required',
            'denominacion' => 'required',
        ]);

        $almacen = Almacen::find($id);
        $almacen->codigo             = (trim($request->input('codigo')));
        $almacen->denominacion              = (trim($request->input('denominacion')));
        $almacen->centro              = (trim($request->input('centro')));
        $almacen->save();
        return redirect()->route('mantenedores.almacenes.list')->with('success', 'almacen editado correctamente.');
    }

    public function active($id)
    {
        Almacen::findOrFail($id)->update(['active' => 1]);
        return redirect()->route('mantenedores.almacenes.list')->with('success', 'almacen activado correctamente.');
    }

    public function inactive($id)
    {
        Almacen::findOrFail($id)->update(['active' => 0]);
        return redirect()->route('mantenedores.almacenes.list')->with('success', 'almacen inactivado correctamente.');
    }
}
