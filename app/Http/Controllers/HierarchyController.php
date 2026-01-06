<?php

namespace App\Http\Controllers;

use App\Hierarchy;
use Illuminate\Http\Request;

class HierarchyController extends Controller
{
    public function index()
    {
        //filtros:
        $hierarchies_filter = Hierarchy::all();
        //filters:
        $query = Hierarchy::query();
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
        $orderby = in_array($orderby, ['descripcion']) ? $orderby : 'descripcion';
        $sorted  = in_array($sorted, ['ASC', 'DESC']) ? $sorted : 'ASC';
        $hierarchies = $query->orderBy($orderby, $sorted)->paginate(20);

        return view('hierarchies.index', compact('hierarchies', 'hierarchies_filter'));
    }
    public function create()
    {
        return view('hierarchies.create');
    }
    public function store(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'descripcion' => 'required',
        ]);
        $hierarchy = new Hierarchy();
        $hierarchy->descripcion             = (trim($request->input('descripcion')) != '') ? $request->input('descripcion') : $hierarchy->descripcion;
        $hierarchy->save();
        return redirect()->route('mantenedores.hierarchies.list')->with('success', 'Jerarquía 1 creada correctamente.');
    }
    public function edit($id)
    {
        $hierarchy = Hierarchy::find($id);
        return view('hierarchies.edit', compact('hierarchy'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'descripcion' => 'required',
        ]);

        $hierarchy = Hierarchy::find($id);
        $hierarchy->descripcion              = (trim($request->input('descripcion')));
        $hierarchy->save();
        return redirect()->route('mantenedores.hierarchies.list')->with('success', 'Jerarquía 1 editada correctamente.');
    }

    public function active($id)
    {
        Hierarchy::findOrFail($id)->update(['active' => 1]);
        return redirect()->route('mantenedores.hierarchies.list')->with('success', 'Jerarquía 1 activada correctamente.');
    }

    public function inactive($id)
    {
        Hierarchy::findOrFail($id)->update(['active' => 0]);
        return redirect()->route('mantenedores.hierarchies.list')->with('success', 'Jerarquía 1 inactivada correctamente.');
    }
}
