<?php

namespace App\Http\Controllers;

use App\ProductType;
use App\Sector;
use Illuminate\Http\Request;

class SectorController extends Controller
{

    public function index()
    {
        //filtros:
        $sectors_filter = Sector::all();

        $sectors_filter = Sector::select('sectores.*', 'product_types.descripcion as nombre_producto')
            ->join('product_types', 'sectores.product_type_id', '=', 'product_types.id')
            ->get();
        //filters:
        $query = Sector::with('product_type');
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
        $sectors = $query->orderBy($orderby, $sorted)->paginate(20);

        return view('sectors.index', compact('sectors', 'sectors_filter'));
    }
    public function create()
    {
        $product_types_id = ProductType::pluck('descripcion', 'id')->toArray();
        return view('sectors.create', compact('product_types_id'));
    }
    public function store(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'codigo' => 'required',
            'descripcion' => 'required',
            'product_type_id' => 'required|unique:sectores,product_type_id,',
        ]);
        $sector = new Sector();
        $sector->codigo             = (trim($request->input('codigo')) != '') ? $request->input('codigo') : $sector->codigo;
        $sector->descripcion             = (trim($request->input('descripcion')) != '') ? $request->input('descripcion') : $sector->descripcion;
        $sector->product_type_id             = (trim($request->input('product_type_id')) != '') ? $request->input('product_type_id') : $sector->product_type_id;
        $sector->save();
        return redirect()->route('mantenedores.sectors.list')->with('success', 'Sector creado correctamente.');
    }
    public function edit($id)
    {
        $sector = Sector::find($id);
        $product_types_id = ProductType::pluck('descripcion', 'id')->toArray();
        return view('sectors.edit', compact('sector', 'product_types_id'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'codigo' => 'required',
            'descripcion' => 'required',
            'product_type_id' => 'required|unique:sectores,product_type_id,' .$id,
        ]);

        $sector = Sector::find($id);
        $sector->codigo              = (trim($request->input('codigo')));
        $sector->descripcion              = (trim($request->input('descripcion')));
        $sector->product_type_id              = (trim($request->input('product_type_id')));
        $sector->save();
        return redirect()->route('mantenedores.sectors.list')->with('success', 'Sector editado correctamente.');
    }

    public function active($id)
    {
        Sector::findOrFail($id)->update(['active' => 1]);
        return redirect()->route('mantenedores.sectors.list')->with('success', 'Sector activado correctamente.');
    }

    public function inactive($id)
    {
        Sector::findOrFail($id)->update(['active' => 0]);
        return redirect()->route('mantenedores.sectors.list')->with('success', 'Sector inactivado correctamente.');
    }
}
