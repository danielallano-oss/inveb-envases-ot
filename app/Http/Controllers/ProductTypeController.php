<?php

namespace App\Http\Controllers;

use App\ProductType;
use Illuminate\Http\Request;

class ProductTypeController extends Controller
{
    public function index()
    {
        //filtros:
        $product_types_filter = ProductType::all();
        //filters:
        $query = ProductType::query();
        if (!is_null(request()->query('codigo'))) {
            $query = $query->whereIn('codigo', request()->query('codigo'));
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
        $productTypes = $query->orderBy($orderby, $sorted)->paginate(20);

        return view('product-types.index', compact('productTypes', 'product_types_filter'));
    }
    public function create()
    {
        return view('product-types.create');
    }
    public function store(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'codigo' => 'required|unique:product_types,codigo',
            'descripcion' => 'required',
            'codigo_sap' => 'required',
        ]);
        $productType = new ProductType();
        $productType->codigo             = (trim($request->input('codigo')) != '') ? $request->input('codigo') : $productType->codigo;
        $productType->codigo_sap             = (trim($request->input('codigo_sap')) != '') ? $request->input('codigo_sap') : $productType->codigo;
        $productType->descripcion             = (trim($request->input('descripcion')) != '') ? $request->input('descripcion') : $productType->descripcion;
        $productType->save();
        return redirect()->route('mantenedores.product-types.list')->with('success', 'Tipo de Producto creado correctamente.');
    }
    public function edit($id)
    {
        $productType = ProductType::find($id);
        return view('product-types.edit', compact('productType'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'codigo' => 'required|unique:product_types,codigo,' . $id,
            'descripcion' => 'required',
            'codigo_sap' => 'required',
        ]);

        $productType = ProductType::find($id);
        $productType->codigo             = (trim($request->input('codigo')));
        $productType->codigo_sap             = (trim($request->input('codigo_sap')));
        $productType->descripcion              = (trim($request->input('descripcion')));
        $productType->save();
        return redirect()->route('mantenedores.product-types.list')->with('success', 'Tipo de Producto editado correctamente.');
    }

    public function active($id)
    {
        ProductType::findOrFail($id)->update(['active' => 1]);
        return redirect()->route('mantenedores.product-types.list')->with('success', 'Tipo de Producto activado correctamente.');
    }

    public function inactive($id)
    {
        ProductType::findOrFail($id)->update(['active' => 0]);
        return redirect()->route('mantenedores.product-types.list')->with('success', 'Tipo de Producto inactivado correctamente.');
    }
}
