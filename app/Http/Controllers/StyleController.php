<?php

namespace App\Http\Controllers;

use App\Style;
use Illuminate\Http\Request;

class StyleController extends Controller
{

    public function index()
    {
        //filtros:
        $styles_filter = Style::all();
        //filters:
        $query = Style::query();
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
        $orderby = in_array($orderby, ['codigo', 'glosa']) ? $orderby : 'glosa';
        $sorted  = in_array($sorted, ['ASC', 'DESC']) ? $sorted : 'ASC';
        $styles = $query->orderBy($orderby, $sorted)->paginate(20);

        return view('styles.index', compact('styles', 'styles_filter'));
    }
    
    public function create()
    {
        return view('styles.create');
    }
    public function store(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'codigo' => 'required|unique:styles,codigo',
            'glosa' => 'required',
            'codigo_armado' => 'required',
        ]);
        $style = new Style();
        $style->codigo          = (trim($request->input('codigo')) != '') ? $request->input('codigo') : $style->codigo;
        $style->glosa           = (trim($request->input('glosa')) != '') ? $request->input('glosa') : $style->glosa;
        $style->codigo_armado   = (trim($request->input('codigo_armado')) != '') ? $request->input('codigo_armado') : $style->codigo_armado;
        $style->grupo_materiales   = (trim($request->input('grupo_materiales')) != '') ? $request->input('grupo_materiales') : $style->grupo_materiales;
        $style->save();
        return redirect()->route('mantenedores.styles.list')->with('success', 'Estilo creado correctamente.');
    }
    public function edit($id)
    {
        $style = Style::find($id);
        return view('styles.edit', compact('style'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'codigo' => 'required|unique:styles,codigo,' . $id,
            'glosa' => 'required',
            'codigo_armado' => 'required',
        ]);

        $style = Style::find($id);
        $style->codigo             = (trim($request->input('codigo')));
        $style->glosa              = (trim($request->input('glosa')));
        $style->codigo_armado      = (trim($request->input('codigo_armado')));
        $style->grupo_materiales   = (trim($request->input('grupo_materiales')));
        $style->save();
        return redirect()->route('mantenedores.styles.list')->with('success', 'Estilo editado correctamente.');
    }

    public function active($id)
    {
        Style::findOrFail($id)->update(['active' => 1]);
        return redirect()->route('mantenedores.styles.list')->with('success', 'Estilo activado correctamente.');
    }

    public function inactive($id)
    {
        Style::findOrFail($id)->update(['active' => 0]);
        return redirect()->route('mantenedores.styles.list')->with('success', 'Estilo inactivado correctamente.');
    }
}
