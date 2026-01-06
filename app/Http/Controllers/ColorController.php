<?php

namespace App\Http\Controllers;

use App\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function index()
    {
        //filtros:
        $colors_filter = Color::all();
        //filters:
        $query = Color::query();
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
        $orderby = in_array($orderby, ['codigo', 'descripcion']) ? $orderby : 'descripcion';
        $sorted  = in_array($sorted, ['ASC', 'DESC']) ? $sorted : 'ASC';
        $colors = $query->orderBy($orderby, $sorted)->paginate(20);

        return view('colors.index', compact('colors', 'colors_filter'));
    }
    public function create()
    {
        return view('colors.create');
    }
    public function store(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'codigo' => 'required|unique:colors,codigo',
            'descripcion' => 'required',
            'texto_breve' => 'required',
        ]);
        $color = new Color();
        $color->codigo             = (trim($request->input('codigo')) != '') ? $request->input('codigo') : $color->codigo;
        $color->descripcion             = (trim($request->input('descripcion')) != '') ? $request->input('descripcion') : $color->descripcion;
        $color->texto_breve             = (trim($request->input('texto_breve')) != '') ? $request->input('texto_breve') : $color->texto_breve;
        $color->save();
        return redirect()->route('mantenedores.colors.list')->with('success', 'Color creado correctamente.');
    }
    public function edit($id)
    {
        $color = Color::find($id);
        return view('colors.edit', compact('color'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'codigo' => 'required|unique:colors,codigo,' . $id,
            'descripcion' => 'required',
            'texto_breve' => 'required',
        ]);

        $color = Color::find($id);
        $color->codigo             = (trim($request->input('codigo')));
        $color->descripcion              = (trim($request->input('descripcion')));
        $color->texto_breve              = (trim($request->input('texto_breve')));
        $color->save();
        return redirect()->route('mantenedores.colors.list')->with('success', 'Color editado correctamente.');
    }

    public function active($id)
    {
        Color::findOrFail($id)->update(['active' => 1]);
        return redirect()->route('mantenedores.colors.list')->with('success', 'Color activado correctamente.');
    }

    public function inactive($id)
    {
        Color::findOrFail($id)->update(['active' => 0]);
        return redirect()->route('mantenedores.colors.list')->with('success', 'Color inactivado correctamente.');
    }
}
