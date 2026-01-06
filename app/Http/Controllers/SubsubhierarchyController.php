<?php

namespace App\Http\Controllers;

use App\Subhierarchy;
use App\Subsubhierarchy;
use Illuminate\Http\Request;

class SubsubhierarchyController extends Controller
{
    public function index()
    {
        //filtros:
        $subsubhierarchies_filter = Subsubhierarchy::all();
        $subhierarchies_filter = Subhierarchy::all();
        //filters:
        $query = Subsubhierarchy::with('subhierarchy');
        if (!is_null(request()->query('id'))) {
            $query = $query->whereIn('id', request()->query('id'));
        }

        if (!is_null(request()->query('subhierarchy_id'))) {
            $query = $query->whereIn('subhierarchy_id', request()->query('subhierarchy_id'));
        }
        // if (!is_null(request()->query('role_id'))) {
        //     $query = $query->whereIn('role_id', request()->query('role_id'));
        // }
        if (!is_null(request()->query('active'))) {
            $query = $query->whereIn('active', request()->query('active'));
        }else{
            $query = $query->whereIn('active', [1]);
        }
        /*order columnns table*/
        $orderby = trim(request()->query('orderby'));
        $sorted  = trim(request()->query('sorted'));
        $orderby = in_array($orderby, ['codigo', 'descripcion']) ? $orderby : 'descripcion';
        $sorted  = in_array($sorted, ['ASC', 'DESC']) ? $sorted : 'ASC';
        $subsubhierarchies = $query->orderBy($orderby, $sorted)->paginate(20);

        return view('subsubhierarchies.index', compact('subsubhierarchies', 'subsubhierarchies_filter','subhierarchies_filter'));
    }
    public function create()
    {
        $subhierarchies_id = Subhierarchy::pluck('descripcion', 'id')->toArray();
        return view('subsubhierarchies.create', compact('subhierarchies_id'));
    }
    public function store(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'descripcion' => 'required',
            'subhierarchy_id' => 'required',
            'jerarquia_sap' => 'required',
        ]);

        // dd(request()->all());
        $subsubhierarchy = new Subsubhierarchy();
        $subsubhierarchy->jerarquia_sap             = (trim($request->input('jerarquia_sap')) != '') ? $request->input('jerarquia_sap') : $subsubhierarchy->jerarquia_sap;
        $subsubhierarchy->descripcion             = (trim($request->input('descripcion')) != '') ? $request->input('descripcion') : $subsubhierarchy->descripcion;
        $subsubhierarchy->subhierarchy_id             = (trim($request->input('subhierarchy_id')) != '') ? $request->input('subhierarchy_id') : $subsubhierarchy->subhierarchy_id;
        $subsubhierarchy->save();
        return redirect()->route('mantenedores.subsubhierarchies.list')->with('success', 'Jerarquía 3 creada correctamente.');
    }
    public function edit($id)
    {
        $subsubhierarchy = Subsubhierarchy::find($id);
        $subhierarchies_id = Subhierarchy::pluck('descripcion', 'id')->toArray();
        return view('subsubhierarchies.edit', compact('subsubhierarchy', 'subhierarchies_id'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'descripcion' => 'required',
            'subhierarchy_id' => 'required',
            'jerarquia_sap' => 'required',
        ]);

        // dd(request()->all());
        $subsubhierarchy = Subsubhierarchy::find($id);
        $subsubhierarchy->jerarquia_sap             = (trim($request->input('jerarquia_sap')));
        $subsubhierarchy->descripcion              = (trim($request->input('descripcion')));
        $subsubhierarchy->subhierarchy_id              = (trim($request->input('subhierarchy_id')));
        $subsubhierarchy->save();
        return redirect()->route('mantenedores.subsubhierarchies.list')->with('success', 'Jerarquía 3 editada correctamente.');
    }

    public function active($id)
    {
        Subsubhierarchy::findOrFail($id)->update(['active' => 1]);
        return redirect()->route('mantenedores.subsubhierarchies.list')->with('success', 'Jerarquía 3 activada correctamente.');
    }

    public function inactive($id)
    {
        Subsubhierarchy::findOrFail($id)->update(['active' => 0]);
        return redirect()->route('mantenedores.subsubhierarchies.list')->with('success', 'Jerarquía 3 inactivada correctamente.');
    }

    public function getJerarquia3()
    {
        // dd(request()->all());
        if (!empty($_GET['subhierarchy_id'])) {

            if(!empty($_GET['jerarquia3']) && !is_null($_GET['jerarquia3'])){
                    
                //obtener las jerarquias activas mas la $_GET['jerarquia2'] de Subhierarchy y obtener descripcion y id
               // $subsubhierarchies = Subsubhierarchy::where('active', 1)->Where('subhierarchy_id', $_GET['subhierarchy_id'])->orWhere('id', $_GET['jerarquia3'])->pluck('descripcion', 'id')->toArray();

               $subsubhierarchies = Subsubhierarchy::where(function($q){ 
                    $q->where('active',1)
                    ->where('subhierarchy_id',$_GET['subhierarchy_id']); 
                })                          
                ->orWhere(function($q){ 
                    $q->where('id',$_GET['jerarquia3'])
                    ->where('subhierarchy_id',$_GET['subhierarchy_id']);
                })
                ->pluck('descripcion', 'id')->toArray();
                 
            }else{
                $subsubhierarchies = Subsubhierarchy::where('active', 1)->where('subhierarchy_id', $_GET['subhierarchy_id'])->pluck('descripcion', 'id')->toArray();
            }

            
            $html = '<option value="" disabled selected>Seleccionar Opción</option>' . optionsSelectArrayfilterSimple($subsubhierarchies, 'subsubhierarchy_id');

            return $html;
        }
        return "";
    }

    public function getJerarquia3ConRubro()
    {
        // dd(request()->all());
        if (!empty($_GET['subhierarchy_id'])) {

            if (request("rubro_id")) {
                $rubro_id = request("rubro_id");
                $subsubhierarchies = Subsubhierarchy::where('rubro_id', $rubro_id)->where('active', 1)->where('subhierarchy_id', $_GET['subhierarchy_id'])->pluck('descripcion', 'id')->toArray();
            } else {
                $subsubhierarchies = Subsubhierarchy::where('active', 1)->where('subhierarchy_id', $_GET['subhierarchy_id'])->pluck('descripcion', 'id')->toArray();
            }

            $html = '<option value="" disabled selected>Seleccionar Opción</option>' . optionsSelectArrayfilterSimple($subsubhierarchies, 'subsubhierarchy_id');

            return $html;
        }
        return "";
    }

    public function getRubro()
    {
        if (!empty(request('subsubhierarchy_id'))) {

            $subsubhierarchy = Subsubhierarchy::find(request('subsubhierarchy_id'));
            // dd($subsubhierarchy);
            return $subsubhierarchy->rubro_id;
        }
        return "";
    }
}
