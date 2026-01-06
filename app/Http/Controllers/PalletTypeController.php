<?php

namespace App\Http\Controllers;

use App\PalletType;
use Illuminate\Http\Request;

class PalletTypeController extends Controller
{
    public function index()
    {
        //filtros:
        $pallet_types_filter = PalletType::all();
        //filters:
        $query = PalletType::query();
        if (!is_null(request()->query('codigo'))) {
            $query = $query->whereIn('codigo', request()->query('codigo'));
        }
        if (!is_null(request()->query('descripcion'))) {
            $query = $query->whereIn('descripcion', request()->query('descripcion'));
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
        $palletTypes = $query->orderBy($orderby, $sorted)->paginate(20);

        return view('pallet-types.index', compact('palletTypes', 'pallet_types_filter'));
    }
    public function create()
    {
        return view('pallet-types.create');
    }
    public function store(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'codigo' => 'required',
            'descripcion' => 'required',
        ]);
        $palletType = new PalletType();
        $palletType->codigo             = (trim($request->input('codigo')) != '') ? $request->input('codigo') : $palletType->codigo;
        $palletType->descripcion             = (trim($request->input('descripcion')) != '') ? $request->input('descripcion') : $palletType->descripcion;
        $palletType->largo = (trim($request->input('largo')) != '') ? $request->input('largo') : $palletType->largo;
        $palletType->ancho = (trim($request->input('ancho')) != '') ? $request->input('ancho') : $palletType->ancho;
        $palletType->cant_pallet_expedicion_26 = (trim($request->input('cant_pallet_expedicion_26')) != '') ? $request->input('cant_pallet_expedicion_26') : $palletType->cant_pallet_expedicion_26;
        $palletType->size_pallet_expedicion_26 = (trim($request->input('size_pallet_expedicion_26')) != '') ? $request->input('size_pallet_expedicion_26') : $palletType->size_pallet_expedicion_26;
        $palletType->cant_pallet_expedicion_27 = (trim($request->input('cant_pallet_expedicion_27')) != '') ? $request->input('cant_pallet_expedicion_27') : $palletType->cant_pallet_expedicion_27;
        $palletType->size_pallet_expedicion_27 = (trim($request->input('size_pallet_expedicion_27')) != '') ? $request->input('size_pallet_expedicion_27') : $palletType->size_pallet_expedicion_27;
        $palletType->cant_pallet_expedicion_28 = (trim($request->input('cant_pallet_expedicion_28')) != '') ? $request->input('cant_pallet_expedicion_28') : $palletType->cant_pallet_expedicion_28;
        $palletType->size_pallet_expedicion_28 = (trim($request->input('size_pallet_expedicion_28')) != '') ? $request->input('size_pallet_expedicion_28') : $palletType->size_pallet_expedicion_28;
        $palletType->cant_pallet_expedicion_29 = (trim($request->input('cant_pallet_expedicion_29')) != '') ? $request->input('cant_pallet_expedicion_29') : $palletType->cant_pallet_expedicion_29;
        $palletType->size_pallet_expedicion_29 = (trim($request->input('size_pallet_expedicion_29')) != '') ? $request->input('size_pallet_expedicion_29') : $palletType->size_pallet_expedicion_29;
        $palletType->cant_pallet_expedicion_30 = (trim($request->input('cant_pallet_expedicion_30')) != '') ? $request->input('cant_pallet_expedicion_30') : $palletType->cant_pallet_expedicion_30;
        $palletType->size_pallet_expedicion_30 = (trim($request->input('size_pallet_expedicion_30')) != '') ? $request->input('size_pallet_expedicion_30') : $palletType->size_pallet_expedicion_30;
        $palletType->cant_pallet_expedicion_36 = (trim($request->input('cant_pallet_expedicion_36')) != '') ? $request->input('cant_pallet_expedicion_36') : $palletType->cant_pallet_expedicion_36;
        $palletType->size_pallet_expedicion_36 = (trim($request->input('size_pallet_expedicion_36')) != '') ? $request->input('size_pallet_expedicion_36') : $palletType->size_pallet_expedicion_36;
        $palletType->cant_pallet_expedicion_40 = (trim($request->input('cant_pallet_expedicion_40')) != '') ? $request->input('cant_pallet_expedicion_40') : $palletType->cant_pallet_expedicion_40;
        $palletType->size_pallet_expedicion_40 = (trim($request->input('size_pallet_expedicion_40')) != '') ? $request->input('size_pallet_expedicion_40') : $palletType->size_pallet_expedicion_40;
        $palletType->cant_pallet_expedicion_41 = (trim($request->input('cant_pallet_expedicion_41')) != '') ? $request->input('cant_pallet_expedicion_41') : $palletType->cant_pallet_expedicion_41;
        $palletType->size_pallet_expedicion_41 = (trim($request->input('size_pallet_expedicion_41')) != '') ? $request->input('size_pallet_expedicion_41') : $palletType->size_pallet_expedicion_41;
        $palletType->cant_pallet_expedicion_42 = (trim($request->input('cant_pallet_expedicion_42')) != '') ? $request->input('cant_pallet_expedicion_42') : $palletType->cant_pallet_expedicion_42;
        $palletType->size_pallet_expedicion_42 = (trim($request->input('size_pallet_expedicion_42')) != '') ? $request->input('size_pallet_expedicion_42') : $palletType->size_pallet_expedicion_42;
        $palletType->cant_pallet_expedicion_43 = (trim($request->input('cant_pallet_expedicion_43')) != '') ? $request->input('cant_pallet_expedicion_43') : $palletType->cant_pallet_expedicion_43;
        $palletType->size_pallet_expedicion_43 = (trim($request->input('size_pallet_expedicion_43')) != '') ? $request->input('size_pallet_expedicion_43') : $palletType->size_pallet_expedicion_43;

        $palletType->save();
        return redirect()->route('mantenedores.pallet-types.list')->with('success', 'Pallet creado correctamente.');
    }
    public function edit($id)
    {
        $palletType = PalletType::find($id);
        return view('pallet-types.edit', compact('palletType'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'codigo' => 'required',
            'descripcion' => 'required',
        ]);

        $palletType = PalletType::find($id);
        $palletType->codigo             = (trim($request->input('codigo')));
        $palletType->descripcion              = (trim($request->input('descripcion')));
        $palletType->largo = trim($request->input('largo'));
        $palletType->ancho = trim($request->input('ancho'));
        $palletType->cant_pallet_expedicion_26 = trim($request->input('cant_pallet_expedicion_26'));
        $palletType->size_pallet_expedicion_26 = trim($request->input('size_pallet_expedicion_26'));
        $palletType->cant_pallet_expedicion_27 = trim($request->input('cant_pallet_expedicion_27'));
        $palletType->size_pallet_expedicion_27 = trim($request->input('size_pallet_expedicion_27'));
        $palletType->cant_pallet_expedicion_28 = trim($request->input('cant_pallet_expedicion_28'));
        $palletType->size_pallet_expedicion_28 = trim($request->input('size_pallet_expedicion_28'));
        $palletType->cant_pallet_expedicion_29 = trim($request->input('cant_pallet_expedicion_29'));
        $palletType->size_pallet_expedicion_29 = trim($request->input('size_pallet_expedicion_29'));
        $palletType->cant_pallet_expedicion_30 = trim($request->input('cant_pallet_expedicion_30'));
        $palletType->size_pallet_expedicion_30 = trim($request->input('size_pallet_expedicion_30'));
        $palletType->cant_pallet_expedicion_36 = trim($request->input('cant_pallet_expedicion_36'));
        $palletType->size_pallet_expedicion_36 = trim($request->input('size_pallet_expedicion_36'));
        $palletType->cant_pallet_expedicion_40 = trim($request->input('cant_pallet_expedicion_40'));
        $palletType->size_pallet_expedicion_40 = trim($request->input('size_pallet_expedicion_40'));
        $palletType->cant_pallet_expedicion_41 = trim($request->input('cant_pallet_expedicion_41'));
        $palletType->size_pallet_expedicion_41 = trim($request->input('size_pallet_expedicion_41'));
        $palletType->cant_pallet_expedicion_42 = trim($request->input('cant_pallet_expedicion_42'));
        $palletType->size_pallet_expedicion_42 = trim($request->input('size_pallet_expedicion_42'));
        $palletType->cant_pallet_expedicion_43 = trim($request->input('cant_pallet_expedicion_43'));
        $palletType->size_pallet_expedicion_43 = trim($request->input('size_pallet_expedicion_43'));

        $palletType->save();
        return redirect()->route('mantenedores.pallet-types.list')->with('success', 'Pallet editado correctamente.');
    }

    public function active($id)
    {
        PalletType::findOrFail($id)->update(['active' => 1]);
        return redirect()->route('mantenedores.pallet-types.list')->with('success', 'Pallet activado correctamente.');
    }

    public function inactive($id)
    {
        PalletType::findOrFail($id)->update(['active' => 0]);
        return redirect()->route('mantenedores.pallet-types.list')->with('success', 'Pallet inactivado correctamente.');
    }
}
