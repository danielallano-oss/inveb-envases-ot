<?php

namespace App\Http\Controllers;

use App\Hierarchy;
use App\Subhierarchy;
use Illuminate\Http\Request;

class SubhierarchyController extends Controller
{

    public function index()
    {
        //filtros:
        $subhierarchies_filter = Subhierarchy::all();
        $hierarchies_filter = Hierarchy::all();
        //filters:
        $query = Subhierarchy::with('hierarchy');
        if (!is_null(request()->query('id'))) {
            $query = $query->whereIn('id', request()->query('id'));
        }

        if (!is_null(request()->query('hierarchy_id'))) {
            $query = $query->whereIn('hierarchy_id', request()->query('hierarchy_id'));
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
        $orderby = in_array($orderby, ['descripcion']) ? $orderby : 'descripcion';
        $sorted  = in_array($sorted, ['ASC', 'DESC']) ? $sorted : 'ASC';
        $subhierarchies = $query->orderBy($orderby, $sorted)->paginate(20);



        return view('subhierarchies.index', compact('subhierarchies', 'subhierarchies_filter','hierarchies_filter'));
    }
    public function create()
    {
        $hierarchies_id = Hierarchy::pluck('descripcion', 'id')->toArray();
        return view('subhierarchies.create', compact('hierarchies_id'));
    }
    public function store(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'descripcion' => 'required',
            'hierarchy_id' => 'required',
        ]);

        // dd(request()->all());
        $subhierarchy = new Subhierarchy();
        $subhierarchy->descripcion             = (trim($request->input('descripcion')) != '') ? $request->input('descripcion') : $subhierarchy->descripcion;
        $subhierarchy->hierarchy_id             = (trim($request->input('hierarchy_id')) != '') ? $request->input('hierarchy_id') : $subhierarchy->hierarchy_id;
        $subhierarchy->save();
        return redirect()->route('mantenedores.subhierarchies.list')->with('success', 'Jerarquía 2 creada correctamente.');
    }
    public function edit($id)
    {
        $subhierarchy = Subhierarchy::find($id);
        $hierarchies_id = Hierarchy::pluck('descripcion', 'id')->toArray();
        return view('subhierarchies.edit', compact('subhierarchy', 'hierarchies_id'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'descripcion' => 'required',
            'hierarchy_id' => 'required',
        ]);

        // dd(request()->all());
        $subhierarchy = Subhierarchy::find($id);
        $subhierarchy->descripcion              = (trim($request->input('descripcion')));
        $subhierarchy->hierarchy_id              = (trim($request->input('hierarchy_id')));
        $subhierarchy->save();
        return redirect()->route('mantenedores.subhierarchies.list')->with('success', 'Jerarquía 2 editada correctamente.');
    }

    public function active($id)
    {
        Subhierarchy::findOrFail($id)->update(['active' => 1]);
        return redirect()->route('mantenedores.subhierarchies.list')->with('success', 'Jerarquía 2 activada correctamente.');
    }

    public function inactive($id)
    {
        Subhierarchy::findOrFail($id)->update(['active' => 0]);
        return redirect()->route('mantenedores.subhierarchies.list')->with('success', 'Jerarquía 2 inactivada correctamente.');
    }

    public function getJerarquia2()
    {
        // dd(request()->all());
        if (!empty($_GET['hierarchy_id'])) {
            // Si se envia rubro filtramos por la relacion a jerarquia 3 q tengan dicho rubro
            if (request("rubro_id")) {
                $subhierarchies = Subhierarchy::whereHas("subsubhierarchies", function ($q) {
                    $q->where('rubro_id', request("rubro_id"));
                })->where('active', 1)->where('hierarchy_id', $_GET['hierarchy_id'])->pluck('descripcion', 'id')->toArray();
                // dd($subhierarchies);
            } else {
                if(!empty($_GET['jerarquia2']) && !is_null($_GET['jerarquia2'])){
                    
                    //obtener las jerarquias activas mas la $_GET['jerarquia2'] de Subhierarchy y obtener descripcion y id
                    //$subhierarchies = Subhierarchy::where('active', 1)->Where('hierarchy_id', $_GET['hierarchy_id'])->orWhere('id', $_GET['jerarquia2'])->pluck('descripcion', 'id')->toArray();
                     
                    $subhierarchies = Subhierarchy::where(function($q){ 
                        $q->where('active',1)
                        ->where('hierarchy_id',$_GET['hierarchy_id']); 
                      })                          
                      ->orWhere(function($q){ 
                        $q->where('id',$_GET['jerarquia2'])
                        ->where('hierarchy_id',$_GET['hierarchy_id']);
                      })
                      ->pluck('descripcion', 'id')->toArray();

                }else{
                    $subhierarchies = Subhierarchy::where('active', 1)->where('hierarchy_id', $_GET['hierarchy_id'])->pluck('descripcion', 'id')->toArray();
                }
                
            }
            // return $equipo_id;

            $html = '<option value="" disabled selected>Seleccionar Opción</option>' . optionsSelectArrayfilterSimple($subhierarchies, 'subhierarchy_id');

            return $html;
        }
        return "";
    }

    public function getJerarquia2AreaHC()
    {
        // dd(request()->all());
        if (!empty($_GET['hierarchy_id'])) {
            // Si se envia rubro filtramos por la relacion a jerarquia 3 q tengan dicho rubro
            if (request("rubro_id")) {
                $subhierarchies = Subhierarchy::whereHas("subsubhierarchies", function ($q) {
                    $q->where('rubro_id', request("rubro_id"));
                })->where('active', 1)->where('hierarchy_id', $_GET['hierarchy_id'])->pluck('descripcion', 'id')->toArray();
                // dd($subhierarchies);
            } else {

                //De lo contrario Solo rubros "alimentos, otros, vinos, aseo y deshidratados" para calculo de area
                $subhierarchies = Subhierarchy::whereHas("subsubhierarchies", function ($q) {
                    $q->whereIn('rubro_id', [12, 13, 14, 18, 19]);
                })->where('active', 1)->where('hierarchy_id', $_GET['hierarchy_id'])->pluck('descripcion', 'id')->toArray();
            }
            // return $equipo_id;

            $html = '<option value="" disabled selected>Seleccionar Opción</option>' . optionsSelectArrayfilterSimple($subhierarchies, 'subhierarchy_id');

            return $html;
        }
        return "";
    }
}
