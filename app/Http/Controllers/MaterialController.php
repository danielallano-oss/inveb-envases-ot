<?php

namespace App\Http\Controllers;

use App\Cad;
use App\Carton;
use App\Client;
use App\Material;
use App\ProductType;
use App\Rayado;
use App\Style;
use App\User;
use Illuminate\Http\Request;

class MaterialController extends Controller
{


    public function index()
    {
        //filtros:
        $materials_filter = Material::all();
        //filters:
        $query = Material::with('product_type', 'carton', 'style', 'rayado', 'client');
        if (!is_null(request()->query('codigo'))) {
            // $query = $query->whereIn('id', request()->query('id'));

            $query = $query->where('codigo', 'LIKE', '%' . request()->query('codigo') . '%');
        }

        if (!is_null(request()->query('descripcion'))) {

            $query = $query->where('descripcion', 'LIKE', '%' . request()->query('descripcion') . '%');
        }

        // dd(request()->all());
        // if (!is_null(request()->query('role_id'))) {
        //     $query = $query->whereIn('role_id', request()->query('role_id'));
        // }
        if (!is_null(request()->query('active'))) {
            $query = $query->whereIn('active', request()->query('active'));
        }
        /*order columnns table*/
        $orderby = trim(request()->query('orderby'));
        $sorted  = trim(request()->query('sorted'));
        $orderby = in_array($orderby, ['id']) ? $orderby : 'id';
        $sorted  = in_array($sorted, ['ASC', 'DESC']) ? $sorted : 'ASC';
        $materials = $query->orderBy($orderby, $sorted)->paginate(20);

        return view('materials.index', compact('materials', 'materials_filter'));
    }
    public function create()
    {
        $cartons = Carton::pluck('codigo', 'id')->toArray();
        $productTypes = ProductType::pluck('descripcion', 'id')->toArray();
        $styles = Style::pluck('glosa', 'id')->toArray();
        $rayados = Rayado::pluck('descripcion', 'id')->toArray();
        $clients = Client::pluck('nombre', 'id')->toArray();
        $cads = Cad::pluck('cad', 'id')->toArray();
        $vendedores = User::where('role_id', 4)->pluck('nombre', 'id')->toArray();

        $estados = [
            1 => 'Activo',
            0 => 'Inactivo',
            2 => 'En proceso',
        ];
        // 1=>'activo';
        // 2=>'En proceso';
        // 0=>'Inactivo';
        return view('materials.create', compact('productTypes', 'cartons', 'styles', 'rayados', 'clients', 'vendedores', 'estados', 'cads'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|unique:materials,codigo',
            'descripcion' => 'required',
            'gramaje'     => 'nullable|numeric',
            'ect'         => 'nullable|numeric',
            'peso'        => 'nullable|numeric',
            'bct_min_lb'  => 'nullable|numeric',
            'bct_min_kg'  => 'nullable|numeric',
            // 'interno_largo' => 'required',
            // 'interno_ancho' => 'required',
            // 'interno_alto' => 'required',
            // 'externo_largo' => 'required',
            // 'externo_ancho' => 'required',
            // 'externo_alto' => 'required',
            // 'largura' => 'required',
            // 'anchura' => 'required',
            // 'distancia_corte1_rayado1' => 'required',
            // 'distancia_rayado1_rayado2' => 'required',
            // 'distancia_rayado2_corte2' => 'required',
            // 'numero_colores' => 'required',
            // 'plano_cad' => 'required',
            // 'carton_id' => 'required',
            // 'product_type_id' => 'required',
            // 'style_id' => 'required',
            // 'rayado_id' => 'required',
            // 'client_id' => 'required',
            // 'gramaje' => 'required',
            // 'ect' => 'required',
            // 'peso' => 'required',
            // 'golpes_largo' => 'required',
            // 'golpes_ancho' => 'required',
            // 'area_hc' => 'required',
            // 'bct_min_lb' => 'required',
            // 'bct_min_kg' => 'required',
        ]);




        // dd(request()->all());
        $material = new Material();
        $material->codigo             = (trim($request->input('codigo')) != '') ? $request->input('codigo') : $material->codigo;
        $material->descripcion             = (trim($request->input('descripcion')) != '') ? $request->input('descripcion') : $material->descripcion;
        $material->fecha_creacion = date('Y-m-d H:i:s');
        $material->creador_id = auth()->user()->id;
        // $material->interno_largo             = (trim($request->input('interno_largo')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('interno_largo'))) : str_replace(",", ".", str_replace('.', '', $material->interno_largo));
        // $material->interno_ancho             = (trim($request->input('interno_ancho')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('interno_ancho'))) : str_replace(",", ".", str_replace('.', '', $material->interno_ancho));
        // $material->interno_alto             = (trim($request->input('interno_alto')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('interno_alto'))) : str_replace(",", ".", str_replace('.', '', $material->interno_alto));
        // $material->externo_largo             = (trim($request->input('externo_largo')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('externo_largo'))) : str_replace(",", ".", str_replace('.', '', $material->externo_largo));
        // $material->externo_ancho             = (trim($request->input('externo_ancho')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('externo_ancho'))) : str_replace(",", ".", str_replace('.', '', $material->externo_ancho));
        // $material->externo_alto             = (trim($request->input('externo_alto')) != '') ? str_replace(",", ".", str_replace('.', '', $request->input('externo_alto'))) : str_replace(",", ".", str_replace('.', '', $material->externo_alto));
        // $material->largura             = (trim($request->input('largura')) != '') ? $request->input('largura') : $material->largura;
        // $material->anchura             = (trim($request->input('anchura')) != '') ? $request->input('anchura') : $material->anchura;
        // $material->distancia_corte1_rayado1             = (trim($request->input('distancia_corte1_rayado1')) != '') ? $request->input('distancia_corte1_rayado1') : $material->distancia_corte1_rayado1;
        // $material->distancia_rayado1_rayado2             = (trim($request->input('distancia_rayado1_rayado2')) != '') ? $request->input('distancia_rayado1_rayado2') : $material->distancia_rayado1_rayado2;
        // $material->distancia_rayado2_corte2             = (trim($request->input('distancia_rayado2_corte2')) != '') ? $request->input('distancia_rayado2_corte2') : $material->distancia_rayado2_corte2;
        $material->numero_colores             = (trim($request->input('numero_colores')) != '') ? $request->input('numero_colores') : $material->numero_colores;
        $material->gramaje             = (trim($request->input('gramaje')) != '') ? $request->input('gramaje') : $material->gramaje;
        $material->ect             = (trim($request->input('ect')) != '') ? $request->input('ect') : $material->ect;
        $material->peso             = (trim($request->input('peso')) != '') ? $request->input('peso') : $material->peso;
        $material->golpes_largo             = (trim($request->input('golpes_largo')) != '') ? $request->input('golpes_largo') : $material->golpes_largo;
        $material->golpes_ancho             = (trim($request->input('golpes_ancho')) != '') ? $request->input('golpes_ancho') : $material->golpes_ancho;
        $material->area_hc             = (trim($request->input('area_hc')) != '') ? $request->input('area_hc') : $material->area_hc;
        $material->bct_min_lb             = (trim($request->input('bct_min_lb')) != '') ? $request->input('bct_min_lb') : $material->bct_min_lb;
        $material->bct_min_kg             = (trim($request->input('bct_min_kg')) != '') ? $request->input('bct_min_kg') : $material->bct_min_kg;
        $material->carton_id             = (trim($request->input('carton_id')[0]) != '') ? $request->input('carton_id')[0] : $material->carton_id;
        $material->product_type_id             = (trim($request->input('product_type_id')[0]) != '') ? $request->input('product_type_id')[0] : $material->product_type_id;
        $material->style_id             = (trim($request->input('style_id')[0]) != '') ? $request->input('style_id')[0] : $material->style_id;
        $material->vendedor_id             = (trim($request->input('vendedor_id')[0]) != '') ? $request->input('vendedor_id')[0] : $material->vendedor_id;
        $material->client_id             = (trim($request->input('client_id')[0]) != '') ? $request->input('client_id')[0] : $material->client_id;
        $material->cad_id             = (trim($request->input('cad_id')[0]) != '') ? $request->input('cad_id')[0] : $material->cad_id;
        $material->active             = (trim($request->input('active')[0]) != '') ? $request->input('active')[0] : $material->active;

        $material->save();
        return redirect()->route('mantenedores.materials.list')->with('success', 'Material creado correctamente.');
    }
    public function edit($id)
    {
        $material = Material::find($id);
        // $cartons = Carton::pluck('codigo', 'id')->toArray();
        // $productTypes = ProductType::pluck('descripcion', 'id')->toArray();
        // $styles = Style::pluck('glosa', 'id')->toArray();
        // $rayados = Rayado::pluck('descripcion', 'id')->toArray();
        // $clients = Client::pluck('nombre', 'id')->toArray();


        $cartons = Carton::pluck('codigo', 'id')->toArray();
        $productTypes = ProductType::pluck('descripcion', 'id')->toArray();
        $styles = Style::pluck('glosa', 'id')->toArray();
        $rayados = Rayado::pluck('descripcion', 'id')->toArray();
        $clients = Client::pluck('nombre', 'id')->toArray();
        $cads = Cad::pluck('cad', 'id')->toArray();
        $vendedores = User::where('role_id', 4)->pluck('nombre', 'id')->toArray();
        $estados = [
            1 => 'Activo',
            0 => 'Inactivo',
            2 => 'En proceso',
        ];

        return view('materials.edit', compact('material', 'productTypes', 'cartons', 'styles', 'rayados', 'clients', 'vendedores', 'cads', 'estados'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'codigo' => 'required|unique:materials,codigo,' . $id,
            'descripcion' => 'required',
            // 'interno_largo' => 'required',
            // 'interno_ancho' => 'required',
            // 'interno_alto' => 'required',
            // 'externo_largo' => 'required',
            // 'externo_ancho' => 'required',
            // 'externo_alto' => 'required',
            // 'largura' => 'required',
            // 'anchura' => 'required',
            // 'distancia_corte1_rayado1' => 'required',
            // 'distancia_rayado1_rayado2' => 'required',
            // 'distancia_rayado2_corte2' => 'required',
            // 'numero_colores' => 'required',
            // 'plano_cad' => 'required',
            'carton_id' => 'required',
            'product_type_id' => 'required',
            'style_id' => 'required',
            // 'rayado_id' => 'required',
            'client_id' => 'required',
            'vendedor_id' => 'required',
            'gramaje'     => 'nullable|numeric',
            'ect'         => 'nullable|numeric',
            'peso'        => 'nullable|numeric',
            'bct_min_lb'  => 'nullable|numeric',
            'bct_min_kg'  => 'nullable|numeric',
            // 'gramaje' => 'required',
            // 'ect' => 'required',
            // 'peso' => 'required',
            // 'golpes_largo' => 'required',
            // 'golpes_ancho' => 'required',
            // 'area_hc' => 'required',
            // 'bct_min_lb' => 'required',
            // 'bct_min_kg' => 'required',
        ]);

        $material = Material::find($id);
        // $material->codigo             = (trim($request->input('codigo')));
        // $material->descripcion             = (trim($request->input('descripcion')));
        // $material->interno_largo             = (trim($request->input('interno_largo')));
        // $material->interno_ancho             = (trim($request->input('interno_ancho')));
        // $material->interno_alto             = (trim($request->input('interno_alto')));
        // $material->externo_largo             = (trim($request->input('externo_largo')));
        // $material->externo_ancho             = (trim($request->input('externo_ancho')));
        // $material->externo_alto             = (trim($request->input('externo_alto')));
        // $material->largura             = (trim($request->input('largura')));
        // $material->anchura             = (trim($request->input('anchura')));
        // $material->distancia_corte1_rayado1             = (trim($request->input('distancia_corte1_rayado1')));
        // $material->distancia_rayado1_rayado2             = (trim($request->input('distancia_rayado1_rayado2')));
        // $material->distancia_rayado2_corte2             = (trim($request->input('distancia_rayado2_corte2')));
        // $material->numero_colores             = (trim($request->input('numero_colores')));
        // $material->plano_cad             = (trim($request->input('plano_cad')));
        // $material->carton_id             = (trim($request->input('carton_id')[0]));
        // $material->product_type_id             = (trim($request->input('product_type_id')[0]));
        // $material->style_id             = (trim($request->input('style_id')[0]));
        // // $material->rayado_id             = (trim($request->input('rayado_id')[0]));
        // $material->client_id             = (trim($request->input('client_id')[0]));


        // $material->codigo             = (trim($request->input('codigo')));
        // $material->descripcion             = (trim($request->input('descripcion')));
        // $material->numero_colores             = (trim($request->input('numero_colores')));
        // $material->gramaje             = (trim($request->input('gramaje')));
        // $material->ect             = (trim($request->input('ect')));
        // $material->peso             = (trim($request->input('peso')) );
        // $material->golpes_largo             = (trim($request->input('golpes_largo')));
        // $material->golpes_ancho             = (trim($request->input('golpes_ancho')));
        // $material->area_hc             = (trim($request->input('area_hc')));
        // $material->bct_min_lb             = (trim($request->input('bct_min_lb')));
        // $material->bct_min_kg             = (trim($request->input('bct_min_kg')) );
        // $material->carton_id             = (trim($request->input('carton_id')[0]));
        // $material->product_type_id             = (trim($request->input('product_type_id')[0]));
        // $material->style_id             = (trim($request->input('style_id')[0]));
        // $material->vendedor_id             = (trim($request->input('vendedor_id')[0]));
        // $material->client_id             = (trim($request->input('client_id')[0]));
        // $material->cad_id             = (trim($request->input('cad_id')[0]));
        // $material->active             = (trim($request->input('active')[0]));
        $material->codigo             = (trim($request->input('codigo')) != '') ? $request->input('codigo') : $material->codigo;
        $material->descripcion             = (trim($request->input('descripcion')) != '') ? $request->input('descripcion') : $material->descripcion;
        $material->numero_colores             = (trim($request->input('numero_colores')) != '') ? $request->input('numero_colores') : $material->numero_colores;
        $material->gramaje             = (trim($request->input('gramaje')) != '') ? $request->input('gramaje') : $material->gramaje;
        $material->ect             = (trim($request->input('ect')) != '') ? $request->input('ect') : $material->ect;
        $material->peso             = (trim($request->input('peso')) != '') ? $request->input('peso') : $material->peso;
        $material->golpes_largo             = (trim($request->input('golpes_largo')) != '') ? $request->input('golpes_largo') : $material->golpes_largo;
        $material->golpes_ancho             = (trim($request->input('golpes_ancho')) != '') ? $request->input('golpes_ancho') : $material->golpes_ancho;
        $material->area_hc             = (trim($request->input('area_hc')) != '') ? $request->input('area_hc') : $material->area_hc;
        $material->bct_min_lb             = (trim($request->input('bct_min_lb')) != '') ? $request->input('bct_min_lb') : $material->bct_min_lb;
        $material->bct_min_kg             = (trim($request->input('bct_min_kg')) != '') ? $request->input('bct_min_kg') : $material->bct_min_kg;
        $material->carton_id             = (trim($request->input('carton_id')[0]) != '') ? $request->input('carton_id')[0] : $material->carton_id;
        $material->product_type_id             = (trim($request->input('product_type_id')[0]) != '') ? $request->input('product_type_id')[0] : $material->product_type_id;
        $material->style_id             = (trim($request->input('style_id')[0]) != '') ? $request->input('style_id')[0] : $material->style_id;
        $material->vendedor_id             = (trim($request->input('vendedor_id')[0]) != '') ? $request->input('vendedor_id')[0] : $material->vendedor_id;
        $material->client_id             = (trim($request->input('client_id')[0]) != '') ? $request->input('client_id')[0] : $material->client_id;
        $material->cad_id             = (trim($request->input('cad_id')[0]) != '') ? $request->input('cad_id')[0] : $material->cad_id;
        $material->active             = (trim($request->input('active')[0]) != '') ? $request->input('active')[0] : $material->active;

        $material->save();
        return redirect()->route('mantenedores.materials.list')->with('success', 'Material editado correctamente.');
    }

    public function active($id)
    {
        Material::findOrFail($id)->update(['active' => 1]);
        return redirect()->route('mantenedores.materials.list')->with('success', 'Material activado correctamente.');
    }

    public function inactive($id)
    {
        Material::findOrFail($id)->update(['active' => 0]);
        return redirect()->route('mantenedores.materials.list')->with('success', 'Material inactivado correctamente.');
    }

    public function findCad()
    {
        // dd(request()->all());
        // dd(request("cad") == "0");
        if (is_null(request("cad")) || trim(request("cad")) === "" || request("cad") == "0") {
            return "false";
        }
        $cad = Cad::where("cad", request("cad"))->first();
        // dd(($cad));
        if (isset($cad)) {
            return "false"; //existe cad
        } else {
            return "true";  //no existe cad
        }
    }

    public function findMaterial()
    {
        // dd(request()->all());
        // dd(request("cad") == "0");
        if (is_null(request("material")) || trim(request("material")) === "" || request("material") == "0") {
            return "false";
        }
        $material = Material::where("codigo", request("material"))->first();
        // dd(($material));
        if (isset($material)) {
            return "false"; //existe material
        } else {
            return "true";  //no existe material
        }
    }
}
