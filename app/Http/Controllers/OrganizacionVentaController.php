<?php

namespace App\Http\Controllers;

use App\OrganizacionVenta;
use Illuminate\Http\Request;

class OrganizacionVentaController extends Controller
{
    public function index()
    {
        //filtros:
        $organizacionventa_filter = OrganizacionVenta::all();

        //filters:
        $query = OrganizacionVenta::query();
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
        $organizacionventa = $query->orderBy($orderby, $sorted)->paginate(20);

        return view('organizacionventa.index', compact('organizacionventa', 'organizacionventa_filter'));
    }
    public function create()
    {

        return view('organizacionventa.create');
    }
    public function store(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'codigo' => 'required|unique:organizaciones_ventas,codigo',
            'descripcion' => 'required',
        ]);
        $organizacionventa = new OrganizacionVenta();
        $organizacionventa->codigo             = (trim($request->input('codigo')) != '') ? $request->input('codigo') : $organizacionventa->codigo;
        $organizacionventa->descripcion             = (trim($request->input('descripcion')) != '') ? $request->input('descripcion') : $organizacionventa->denominacion;
        $organizacionventa->save();
        return redirect()->route('mantenedores.organizacion-venta.list')->with('success', 'Organización venta creado correctamente.');
    }
    public function edit($id)
    {
        $organizacionventa = OrganizacionVenta::find($id);

        return view('organizacionventa.edit', compact('organizacionventa'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'codigo' => 'required|unique:organizaciones_ventas,codigo,' . $id,
            'descripcion' => 'required',
        ]);

        $organizacionventa = OrganizacionVenta::find($id);
        $organizacionventa->codigo             = (trim($request->input('codigo')));
        $organizacionventa->descripcion              = (trim($request->input('descripcion')));
        $organizacionventa->save();
        return redirect()->route('mantenedores.organizacion-venta.list')->with('success', 'Organización venta editado correctamente.');
    }

    public function active($id)
    {
        OrganizacionVenta::findOrFail($id)->update(['active' => 1]);
        return redirect()->route('mantenedores.organizacion-venta.list')->with('success', 'Organización venta activado correctamente.');
    }

    public function inactive($id)
    {
        OrganizacionVenta::findOrFail($id)->update(['active' => 0]);
        return redirect()->route('mantenedores.organizacion-venta.list')->with('success', 'Organización venta inactivado correctamente.');
    }
}
