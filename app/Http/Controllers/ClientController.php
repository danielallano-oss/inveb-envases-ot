<?php

namespace App\Http\Controllers;

use App\CiudadesFlete;
use App\Client;
use App\PalletType;
use App\Pais;
use App\Fsc;
use App\Installation;
use App\IndicacionEspecial;
use App\PalletQa;
use App\PalletTagFormat;
use App\Mail\NotificarAdminNuevoCliente;
use App\Subhierarchy;
use App\ClasificacionCliente;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Auth;

class ClientController extends Controller
{
    public function index()
    {
        //filtros:
        $clients_filter = Client::all();
        //filters:
        $query = Client::query();
        if (!is_null(request()->query('id'))) {
            $query = $query->whereIn('id', request()->query('id'));
        }
        // if (!is_null(request()->query('role_id'))) {
        //     $query = $query->whereIn('role_id', request()->query('role_id'));
        // }
        if (!is_null(request()->query('active'))) {
            $query = $query->whereIn('active', request()->query('active'));
        }
        if (!is_null(request()->query('clasificacion'))) {
            $query = $query->whereIn('clasificacion', request()->query('clasificacion'));
        }

        /*order columnns table*/
        $orderby = trim(request()->query('orderby'));
        $sorted  = trim(request()->query('sorted'));
        $orderby = in_array($orderby, ['rut', 'nombre']) ? $orderby : 'nombre';
        $sorted  = in_array($sorted, ['ASC', 'DESC']) ? $sorted : 'ASC';
        $clients = $query->orderBy($orderby, $sorted)->paginate(20);

        $clasificaciones = ClasificacionCliente::where('active',1)->pluck('name', 'id')->toArray();

        return view('clients.index', compact('clients', 'clients_filter','clasificaciones'));
    }
    public function create()
    {
        $palletTypes = PalletType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $fsc = Fsc::where('active', 1)->pluck('descripcion', 'codigo')->toArray();
        $targetMarket = Pais::where('active', 1)->orderBy('name', 'ASC')->pluck('name', 'id')->toArray();
        $ciudades = CiudadesFlete::pluck('ciudad', 'id')->toArray();
        $codigo_carga=Auth::user()->id.date('ymdhis');
        $palletQa = PalletQa::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $palletTagFormat = PalletTagFormat::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $clasificaciones = ClasificacionCliente::where('active',1)->where('visible',0)->pluck('name', 'id')->toArray();

        return view('clients.create', compact("ciudades","palletTypes","fsc","targetMarket","codigo_carga","palletQa","palletTagFormat","clasificaciones"));
    }
    public function store(Request $request)
    {
        //dd(request()->all(),request("cliente_id"));
        $request->validate([
            'rut' => 'required|unique:clients,rut',
            'nombre' => 'required',
            'email_contacto' => 'nullable|email',
            'phone_contacto' =>  array(
                'nullable',
                'regex:/^(\+56)?(\s?)(0?[92])(\s?)[987654321]\d{7}$/'
            ),
            'email_contacto_2' => 'nullable|email',
            'phone_contacto_2' =>  array(
                'nullable',
                'regex:/^(\+56)?(\s?)(0?[92])(\s?)[987654321]\d{7}$/'
            ),
            'email_contacto_3' => 'nullable|email',
            'phone_contacto_3' =>  array(
                'nullable',
                'regex:/^(\+56)?(\s?)(0?[92])(\s?)[987654321]\d{7}$/'
            ),
            'email_contacto_4' => 'nullable|email',
            'phone_contacto_4' =>  array(
                'nullable',
                'regex:/^(\+56)?(\s?)(0?[92])(\s?)[987654321]\d{7}$/'
            ),
            'email_contacto_5' => 'nullable|email',
            'phone_contacto_5' =>  array(
                'nullable',
                'regex:/^(\+56)?(\s?)(0?[92])(\s?)[987654321]\d{7}$/'
            ),
        ], [
            'email_contacto.email' => 'Por favor, ingresa una dirección de correo válida',
            'email_contacto_2.email' => 'Por favor, ingresa una dirección de correo válida',
            'email_contacto_3.email' => 'Por favor, ingresa una dirección de correo válida',
            'email_contacto_4.email' => 'Por favor, ingresa una dirección de correo válida',
            'email_contacto_5.email' => 'Por favor, ingresa una dirección de correo válida',
            'phone_contacto.regex' => 'Formato de Telefono: +56912345678',
            'phone_contacto_2.regex' => 'Formato de Telefono: +56912345678',
            'phone_contacto_3.regex' => 'Formato de Telefono: +56912345678',
            'phone_contacto_4.regex' => 'Formato de Telefono: +56912345678',
            'phone_contacto_5.regex' => 'Formato de Telefono: +56912345678',
        ]);
        $client = new Client();
        $client->nombre             = (trim($request->input('nombre')) != '') ? $request->input('nombre') : $client->nombre;
        $client->rut              = (trim($request->input('rut')) != '') ? $request->input('rut') : $client->rut;
        $client->fecha_ingreso              = Carbon::now();
        $client->direccion              = (trim($request->input('direccion')) != '') ? $request->input('direccion') : $client->direccion;
        $client->poblacion              = (trim($request->input('poblacion')) != '') ? $request->input('poblacion') : $client->poblacion;
        $client->telefono              = (trim($request->input('telefono')) != '') ? $request->input('telefono') : $client->telefono;
        $client->codigo              = (trim($request->input('codigo')) != '') ? $request->input('codigo') : $client->codigo;
        $client->codigo_zona              = (trim($request->input('codigo_zona')) != '') ? $request->input('codigo_zona') : $client->codigo_zona;
        $client->nacional              = (trim($request->input('nacional')) != '') ? $request->input('nacional') : $client->nacional;
        $client->clasificacion    = (trim($request->input('clasificacion')) != '') ? $request->input('clasificacion') : 4;
        $client->nombre_contacto  = (trim($request->input('nombre_contacto')) != '') ? $request->input('nombre_contacto') : $client->nombre_contacto;
        $client->cargo_contacto   = (trim($request->input('cargo_contacto')) != '') ? $request->input('cargo_contacto') : $client->cargo_contacto;
        $client->email_contacto   = (trim($request->input('email_contacto')) != '') ? $request->input('email_contacto') : $client->email_contacto;
        $client->phone_contacto   = (trim($request->input('phone_contacto')) != '') ? $request->input('phone_contacto') : $client->phone_contacto;
        $client->comuna_contacto   = (trim($request->input('comuna_contacto')) != '') ? $request->input('comuna_contacto') : $client->comuna_contacto;
        $client->direccion_contacto   = (trim($request->input('direccion_contacto')) != '') ? $request->input('direccion_contacto') : $client->direccion_contacto;
        $client->active_contacto           = (trim($request->input('active_contacto')) != '') ? $request->input('active_contacto') : 'inactivo';

        $client->nombre_contacto_2  = (trim($request->input('nombre_contacto_2')) != '') ? $request->input('nombre_contacto_2') : $client->nombre_contacto_2;
        $client->cargo_contacto_2   = (trim($request->input('cargo_contacto_2')) != '') ? $request->input('cargo_contacto_2') : $client->cargo_contacto_2;
        $client->email_contacto_2   = (trim($request->input('email_contacto_2')) != '') ? $request->input('email_contacto_2') : $client->email_contacto_2;
        $client->phone_contacto_2   = (trim($request->input('phone_contacto_2')) != '') ? $request->input('phone_contacto_2') : $client->phone_contacto_2;
        $client->comuna_contacto_2   = (trim($request->input('comuna_contacto_2')) != '') ? $request->input('comuna_contacto_2') : $client->comuna_contacto_2;
        $client->direccion_contacto_2   = (trim($request->input('direccion_contacto_2')) != '') ? $request->input('direccion_contacto_2') : $client->direccion_contacto_2;
        $client->active_contacto_2           = (trim($request->input('active_contacto_2')) != '') ? $request->input('active_contacto_2') : 'inactivo';

        $client->nombre_contacto_3  = (trim($request->input('nombre_contacto_3')) != '') ? $request->input('nombre_contacto_3') : $client->nombre_contacto_3;
        $client->cargo_contacto_3   = (trim($request->input('cargo_contacto_3')) != '') ? $request->input('cargo_contacto_3') : $client->cargo_contacto_3;
        $client->email_contacto_3   = (trim($request->input('email_contacto_3')) != '') ? $request->input('email_contacto_3') : $client->email_contacto_3;
        $client->phone_contacto_3   = (trim($request->input('phone_contacto_3')) != '') ? $request->input('phone_contacto_3') : $client->phone_contacto_3;
        $client->comuna_contacto_3   = (trim($request->input('comuna_contacto_3')) != '') ? $request->input('comuna_contacto_3') : $client->comuna_contacto_3;
        $client->direccion_contacto_3   = (trim($request->input('direccion_contacto_3')) != '') ? $request->input('direccion_contacto_3') : $client->direccion_contacto_3;
        $client->active_contacto_3           = (trim($request->input('active_contacto_3')) != '') ? $request->input('active_contacto_3') : 'inactivo';

        $client->nombre_contacto_4  = (trim($request->input('nombre_contacto_4')) != '') ? $request->input('nombre_contacto_4') : $client->nombre_contacto_4;
        $client->cargo_contacto_4   = (trim($request->input('cargo_contacto_4')) != '') ? $request->input('cargo_contacto_4') : $client->cargo_contacto_4;
        $client->email_contacto_4   = (trim($request->input('email_contacto_4')) != '') ? $request->input('email_contacto_4') : $client->email_contacto_4;
        $client->phone_contacto_4   = (trim($request->input('phone_contacto_4')) != '') ? $request->input('phone_contacto_4') : $client->phone_contacto_4;
        $client->comuna_contacto_4   = (trim($request->input('comuna_contacto_4')) != '') ? $request->input('comuna_contacto_4') : $client->comuna_contacto_4;
        $client->direccion_contacto_4   = (trim($request->input('direccion_contacto_4')) != '') ? $request->input('direccion_contacto_4') : $client->direccion_contacto_4;
        $client->active_contacto_4           = (trim($request->input('active_contacto_4')) != '') ? $request->input('active_contacto_4') : 'inactivo';

        $client->nombre_contacto_5  = (trim($request->input('nombre_contacto_5')) != '') ? $request->input('nombre_contacto_5') : $client->nombre_contacto_5;
        $client->cargo_contacto_5   = (trim($request->input('cargo_contacto_5')) != '') ? $request->input('cargo_contacto_5') : $client->cargo_contacto_5;
        $client->email_contacto_5   = (trim($request->input('email_contacto_5')) != '') ? $request->input('email_contacto_5') : $client->email_contacto_5;
        $client->phone_contacto_5   = (trim($request->input('phone_contacto_5')) != '') ? $request->input('phone_contacto_5') : $client->phone_contacto_5;
        $client->comuna_contacto_5   = (trim($request->input('comuna_contacto_5')) != '') ? $request->input('comuna_contacto_5') : $client->comuna_contacto_5;
        $client->direccion_contacto_5   = (trim($request->input('direccion_contacto_5')) != '') ? $request->input('direccion_contacto_5') : $client->direccion_contacto_5;
        $client->active_contacto_5           = (trim($request->input('active_contacto_5')) != '') ? $request->input('active_contacto_5') : 'inactivo';

        $installations = Installation::where('client_id',$request->input('codigo_carga'))->get();
        $client->instalaciones           = $installations->count();

        $client->save();

        $installation_update = Installation::where('client_id',$request->input('codigo_carga'))
                                            ->update(['client_id' => $client->id]);

        $instrucions_update = IndicacionEspecial::where('client_id',$request->input('codigo_carga'))
                                            ->update(['client_id' => $client->id]);

        if (!auth()->user()->isAdmin()) {
            Mail::to(['maria.botella@cmpc.com'])->send(new NotificarAdminNuevoCliente($client));
        }
        return redirect()->route('mantenedores.clients.list')->with('success', 'Cliente creado correctamente.');
    }

    public function store_installation()
    {

        $html='';

        $installation = new Installation();
        $installation->nombre               = (empty($_GET['nombre']))?null:$_GET['nombre'];
        $installation->client_id            = (empty($_GET['cliente']))?null:$_GET['cliente'];
        $installation->tipo_pallet          = ($_GET['tipo_pallet']=='')?null:$_GET['tipo_pallet'];
        $installation->altura_pallet        = (empty($_GET['altura_pallet']))?null:$_GET['altura_pallet'];
        $installation->sobresalir_carga     = ($_GET['sobresalir_carga']=='')?null:$_GET['sobresalir_carga'];
        $installation->bulto_zunchado       = ($_GET['bulto_zunchado']=='')?null:$_GET['bulto_zunchado'];
        $installation->formato_etiqueta     = (empty($_GET['formato_etiqueta']))?null:$_GET['formato_etiqueta'];
        $installation->etiquetas_pallet     = (empty($_GET['etiquetas_pallet']))?null:$_GET['etiquetas_pallet'];
        $installation->termocontraible      = ($_GET['termocontraible']=='')?null:$_GET['termocontraible'];
        $installation->fsc                  = ($_GET['fsc']=='')?null:$_GET['fsc'];
        $installation->pais_mercado_destino = ($_GET['pais_mercado_destino']=='')?null:$_GET['pais_mercado_destino'];
        $installation->certificado_calidad  = ($_GET['certificado_calidad']=='')?null:$_GET['certificado_calidad'];
        $installation->nombre_contacto      = (empty($_GET['nombre_contacto']))?null:$_GET['nombre_contacto'];
        $installation->cargo_contacto       = (empty($_GET['cargo_contacto']))?null:$_GET['cargo_contacto'];
        $installation->email_contacto       = (empty($_GET['email_contacto']))?null:$_GET['email_contacto'];
        $installation->phone_contacto       = (empty($_GET['phone_contacto']))?null:str_replace("*","+",$_GET['phone_contacto']);
        $installation->direccion_contacto   = (empty($_GET['direccion_contacto']))?null:$_GET['direccion_contacto'];
        $installation->comuna_contacto      = ($_GET['comuna_contacto']=='')?null:$_GET['comuna_contacto'];
        $installation->active_contacto      = ($_GET['active_contacto']=='')?'inactivo':$_GET['active_contacto'];
        $installation->nombre_contacto_2    = (empty($_GET['nombre_contacto_2']))?null:$_GET['nombre_contacto_2'];
        $installation->cargo_contacto_2     = (empty($_GET['cargo_contacto_2']))?null:$_GET['cargo_contacto_2'];
        $installation->email_contacto_2     = (empty($_GET['email_contacto_2']))?null:$_GET['email_contacto_2'];
        $installation->phone_contacto_2     = (empty($_GET['phone_contacto_2']))?null:str_replace("*","+",$_GET['phone_contacto_2']);
        $installation->direccion_contacto_2 = (empty($_GET['direccion_contacto_2']))?null:$_GET['direccion_contacto_2'];
        $installation->comuna_contacto_2    = ($_GET['comuna_contacto_2']=='')?null:$_GET['comuna_contacto_2'];
        $installation->active_contacto_2    = ($_GET['active_contacto_2']=='')?'inactivo':$_GET['active_contacto_2'];
        $installation->nombre_contacto_3    = (empty($_GET['nombre_contacto_3']))?null:$_GET['nombre_contacto_3'];
        $installation->cargo_contacto_3     = (empty($_GET['cargo_contacto_3']))?null:$_GET['cargo_contacto_3'];
        $installation->email_contacto_3     = (empty($_GET['email_contacto_3']))?null:$_GET['email_contacto_3'];
        $installation->phone_contacto_3     = (empty($_GET['phone_contacto_3']))?null:str_replace("*","+",$_GET['phone_contacto_3']);
        $installation->direccion_contacto_3 = (empty($_GET['direccion_contacto_3']))?null:$_GET['direccion_contacto_3'];
        $installation->comuna_contacto_3    = ($_GET['comuna_contacto_3']=='')?null:$_GET['comuna_contacto_3'];
        $installation->active_contacto_3    = ($_GET['active_contacto_3']=='')?'inactivo':$_GET['active_contacto_3'];
        $installation->nombre_contacto_4    = (empty($_GET['nombre_contacto_4']))?null:$_GET['nombre_contacto_4'];
        $installation->cargo_contacto_4     = (empty($_GET['cargo_contacto_4']))?null:$_GET['cargo_contacto_4'];
        $installation->email_contacto_4     = (empty($_GET['email_contacto_4']))?null:$_GET['email_contacto_4'];
        $installation->phone_contacto_4     = (empty($_GET['phone_contacto_4']))?null:str_replace("*","+",$_GET['phone_contacto_4']);
        $installation->direccion_contacto_4 = (empty($_GET['direccion_contacto_4']))?null:$_GET['direccion_contacto_4'];
        $installation->comuna_contacto_4    = ($_GET['comuna_contacto_4']=='')?null:$_GET['comuna_contacto_4'];
        $installation->active_contacto_4    = ($_GET['active_contacto_4']=='')?'inactivo':$_GET['active_contacto_4'];
        $installation->nombre_contacto_5    = (empty($_GET['nombre_contacto_5']))?null:$_GET['nombre_contacto_5'];
        $installation->cargo_contacto_5     = (empty($_GET['cargo_contacto_5']))?null:$_GET['cargo_contacto_5'];
        $installation->email_contacto_5     = (empty($_GET['email_contacto_5']))?null:$_GET['email_contacto_5'];
        $installation->phone_contacto_5     = (empty($_GET['phone_contacto_5']))?null:str_replace("*","+",$_GET['phone_contacto_5']);
        $installation->direccion_contacto_5 = (empty($_GET['direccion_contacto_5']))?null:$_GET['direccion_contacto_5'];
        $installation->comuna_contacto_5    = ($_GET['comuna_contacto_5']=='')?null:$_GET['comuna_contacto_5'];
        $installation->active_contacto_5    = ($_GET['active_contacto_5']=='')?'inactivo':$_GET['active_contacto_5'];
        $installation->active               = ($_GET['active']=='')?null:$_GET['active'];
        $installation->deleted              = 0;
        $installation->save();

        $installations = Installation::where('client_id',$_GET['cliente'])->get();

        foreach ($installations as $installation) {
            $html .='<tr style="background: #e4e6e4;">';
            $html .='<td>';
            $html .='<h3>&nbsp;&nbsp;'.$installation->nombre.'&nbsp;&nbsp;';
            $html .='<a herf="#" data-toggle="modal" data-target="#modal-editar-planta" data-editar="'.$installation->id.'">';
            $html .='<div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>';
            $html .='</a></h3>';
            $html .='</td>';
            $html .='</tr>';
            $html .='<tr>';
            $html .='<td>';
            $html .='<table class="table table-sm table-bordered" width="100%">';
            $html .='<thead>';
            $html .='<tr><th colspan="10"><h5><b>Datos Paletizado</b></h5></th></tr>';
            $html .='<tr>';
            $html .='<th style="width: 350px;"><b>Tipo Pallet</b></th>';
            $html .='<th style="width: 120px;"><b>Altura Pallet</b></th>';
            $html .='<th style="width: 120px;"><b>Sobresalir Carga</b></th>';
            $html .='<th style="width: 120px;"><b>Bulto Zunchado</b></th>';
            $html .='<th style="width: 120px;"><b>Formato Etiqueta</b></th>';
            $html .='<th style="width: 120px;"><b>Etiquetas Pallet</b></th>';
            $html .='<th style="width: 150px;"><b>Termocontraible</b></th>';
            $html .='<th style="width: 200px;"><b>Fsc</b></th>';
            $html .='<th style="width: 120px;"><b>Pais Mercado/Destino</b></th>';
            $html .='<th style="width: 120px;"><b>Certificado de Calidad</b></th>';
            $html .='</tr>';
            $html .='</thead>';
            $html .='<tbody>';
            $html .='<tr>';
            $html .=(is_null($installation->tipo_pallet))?'<td>N/A</td>':'<td>'.$installation->TipoPalleT->descripcion.'</td>';
            $html .=(is_null($installation->altura_pallet))?'<td>N/A</td>':'<td>'.$installation->altura_pallet.'</td>';
            $html .=($installation->sobresalir_carga==1)?'<td>SI</td>':'<td>NO</td>';
            $html .=($installation->bulto_zunchado==1)?'<td>SI</td>':'<td>NO</td>';
            $html .=(is_null($installation->formato_etiqueta))?'<td>N/A</td>':'<td>'.$installation->formato_etiqueta_pallet->descripcion.'</td>';
            $html .=(is_null($installation->etiquetas_pallet))?'<td>N/A</td>':'<td>'.$installation->etiquetas_pallet.'</td>';
            $html .=($installation->termocontraible==1)?'<td>SI</td>':'<td>NO</td>';
            $html .=(is_null($installation->fsc))?'<td>N/A</td>':'<td>'.$installation->Fsc->descripcion.'</td>';
            $html .=(is_null($installation->pais_mercado_destino))?'<td>N/A</td>':'<td>'.$installation->TargetMarket->name.'</td>';
            $html .=(is_null($installation->certificado_calidad))?'<td>N/A</td>':'<td>'.$installation->qa->descripcion.'</td>';
            $html .='</tr>';
            $html .='</tbody>';
            $html .='</table>';
            $html .='</td>';
            $html .='</tr>';
            $html .='<tr>';
            $html .='<td>';
            $html .='<table class="table table-sm table-bordered" width="100%">';
            $html .='<thead>';
            $html .='<tr><th colspan="8"><h5><b>Listado de Contactos</b></h5></th></tr>';
            $html .='<tr>';
            $html .='<th style="width: 150px;"><b>Descripción</b></th>';
            $html .='<th style="width: 150px;"><b>Nombre</b></th>';
            $html .='<th style="width: 150px;"><b>Cargo</b></th>';
            $html .='<th style="width: 150px;"><b>Correo</b></th>';
            $html .='<th style="width: 150px;"><b>Teléfono</b></th>';
            $html .='<th style="width: 150px;"><b>Comuna</b></th>';
            $html .='<th style="width: 300px;"><b>Dirección</b></th>';
            $html .='<th style="width: 100px;"><b>Estado</b></th>';
            $html .='</tr>';
            $html .='</thead>';
            $html .='<tbody>';
            $html .='<tr>';
            $html .='<td><b>Contacto 1</b></td>';
            $html .=(is_null($installation->nombre_contacto))?'<td>N/A</td>':'<td>'.$installation->nombre_contacto.'</td>';
            $html .=(is_null($installation->cargo_contacto))?'<td>N/A</td>':'<td>'.$installation->cargo_contacto.'</td>';
            $html .=(is_null($installation->email_contacto))?'<td>N/A</td>':'<td>'.$installation->email_contacto.'</td>';
            $html .=(is_null($installation->phone_contacto))?'<td>N/A</td>':'<td>'.$installation->phone_contacto.'</td>';
            $html .=(is_null($installation->comuna_contacto))?'<td>N/A</td>':'<td>'.$installation->Comuna->ciudad.'</td>';
            $html .=(is_null($installation->direccion_contacto))?'<td>N/A</td>':'<td>'.$installation->direccion_contacto.'</td>';
            $html .=(is_null($installation->active_contacto))?'<td>N/A</td>':'<td>'.$installation->active_contacto.'</td>';
            $html .='</tr>';
            $html .='<tr>';
            $html .='<td><b>Contacto 2</b></td>';
            $html .=(is_null($installation->nombre_contacto_2))?'<td>N/A</td>':'<td>'.$installation->nombre_contacto_2.'</td>';
            $html .=(is_null($installation->cargo_contacto_2))?'<td>N/A</td>':'<td>'.$installation->cargo_contacto_2.'</td>';
            $html .=(is_null($installation->email_contacto_2))?'<td>N/A</td>':'<td>'.$installation->email_contacto_2.'</td>';
            $html .=(is_null($installation->phone_contacto_2))?'<td>N/A</td>':'<td>'.$installation->phone_contacto_2.'</td>';
            $html .=(is_null($installation->comuna_contacto_2))?'<td>N/A</td>':'<td>'.$installation->Comuna_2->ciudad.'</td>';
            $html .=(is_null($installation->direccion_contacto_2))?'<td>N/A</td>':'<td>'.$installation->direccion_contacto_2.'</td>';
            $html .=(is_null($installation->active_contacto_2))?'<td>N/A</td>':'<td>'.$installation->active_contacto_2.'</td>';
            $html .='</tr>';
            $html .='<tr>';
            $html .='<td><b>Contacto 3</b></td>';
            $html .=(is_null($installation->nombre_contacto_3))?'<td>N/A</td>':'<td>'.$installation->nombre_contacto_3.'</td>';
            $html .=(is_null($installation->cargo_contacto_3))?'<td>N/A</td>':'<td>'.$installation->cargo_contacto_3.'</td>';
            $html .=(is_null($installation->email_contacto_3))?'<td>N/A</td>':'<td>'.$installation->email_contacto_3.'</td>';
            $html .=(is_null($installation->phone_contacto_3))?'<td>N/A</td>':'<td>'.$installation->phone_contacto_3.'</td>';
            $html .=(is_null($installation->comuna_contacto_3))?'<td>N/A</td>':'<td>'.$installation->Comuna_3->ciudad.'</td>';
            $html .=(is_null($installation->direccion_contacto_3))?'<td>N/A</td>':'<td>'.$installation->direccion_contacto_3.'</td>';
            $html .=(is_null($installation->active_contacto_3))?'<td>N/A</td>':'<td>'.$installation->active_contacto_3.'</td>';
            $html .='</tr>';
            $html .='<tr>';
            $html .='<td><b>Contacto 4</b></td>';
            $html .=(is_null($installation->nombre_contacto_4))?'<td>N/A</td>':'<td>'.$installation->nombre_contacto_4.'</td>';
            $html .=(is_null($installation->cargo_contacto_4))?'<td>N/A</td>':'<td>'.$installation->cargo_contacto_4.'</td>';
            $html .=(is_null($installation->email_contacto_4))?'<td>N/A</td>':'<td>'.$installation->email_contacto_4.'</td>';
            $html .=(is_null($installation->phone_contacto_4))?'<td>N/A</td>':'<td>'.$installation->phone_contacto_4.'</td>';
            $html .=(is_null($installation->comuna_contacto_4))?'<td>N/A</td>':'<td>'.$installation->Comuna_4->ciudad.'</td>';
            $html .=(is_null($installation->direccion_contacto_4))?'<td>N/A</td>':'<td>'.$installation->direccion_contacto_4.'</td>';
            $html .=(is_null($installation->active_contacto_4))?'<td>N/A</td>':'<td>'.$installation->active_contacto_4.'</td>';
            $html .='</tr>';
            $html .='<tr>';
            $html .='<td><b>Contacto 5</b></td>';
            $html .=(is_null($installation->nombre_contacto_5))?'<td>N/A</td>':'<td>'.$installation->nombre_contacto_5.'</td>';
            $html .=(is_null($installation->cargo_contacto_5))?'<td>N/A</td>':'<td>'.$installation->cargo_contacto_5.'</td>';
            $html .=(is_null($installation->email_contacto_5))?'<td>N/A</td>':'<td>'.$installation->email_contacto_5.'</td>';
            $html .=(is_null($installation->phone_contacto_5))?'<td>N/A</td>':'<td>'.$installation->phone_contacto_5.'</td>';
            $html .=(is_null($installation->comuna_contacto_5))?'<td>N/A</td>':'<td>'.$installation->Comuna_5->ciudad.'</td>';
            $html .=(is_null($installation->direccion_contacto_5))?'<td>N/A</td>':'<td>'.$installation->direccion_contacto_5.'</td>';
            $html .=(is_null($installation->active_contacto_5))?'<td>N/A</td>':'<td>'.$installation->active_contacto_5.'</td>';
            $html .='</tr>';
            $html .='</tbody>';
            $html .='</table>';
            $html .='</td>';
            $html .='</tr>';
        }

        if($_GET['tipo']=='edit'){
            $cliente= Client::where('id',$_GET['cliente'])->get();
            $num_installations= $cliente[0]->instalaciones + 1;

            $cliente_update = Client::where('id',$_GET['cliente'])->update(['instalaciones' => $num_installations]);
        }

        return $html;

    }

    public function edit($id)
    {
        $client = Client::find($id);
        $ciudades = CiudadesFlete::pluck('ciudad', 'id')->toArray();
        $palletTypes = PalletType::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $fsc = Fsc::where('active', 1)->pluck('descripcion', 'codigo')->toArray();
        $targetMarket = Pais::where('active', 1)->orderBy('name', 'ASC')->pluck('name', 'id')->toArray();
        $installations = Installation::where('deleted', 0)->where('client_id', $id)->get();
        $indicaciones = IndicacionEspecial::where('deleted', 0)->where('client_id', $id)->get();
        $palletQa = PalletQa::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $palletTagFormat = PalletTagFormat::where('active', 1)->pluck('descripcion', 'id')->toArray();
        $clasificaciones = ClasificacionCliente::where('active',1)->where('visible',0)->pluck('name', 'id')->toArray();
        //dd($installations);
        return view('clients.edit', compact('client', 'ciudades','palletTypes','fsc','targetMarket','installations',"palletQa","palletTagFormat",'indicaciones','clasificaciones'));
    }

    public function edit_installation()
    {
        $installation = Installation::find($_GET['instalacion']);

        return $installation;
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required',
            'email_contacto' => 'nullable|email',
            'phone_contacto' =>  array(
                'nullable',
                'regex:/^(\+56)?(\s?)(0?[92])(\s?)[987654321]\d{7}$/'
            ),
            'email_contacto_2' => 'nullable|email',
            'phone_contacto_2' =>  array(
                'nullable',
                'regex:/^(\+56)?(\s?)(0?[92])(\s?)[987654321]\d{7}$/'
            ),
            'email_contacto_3' => 'nullable|email',
            'phone_contacto_3' =>  array(
                'nullable',
                'regex:/^(\+56)?(\s?)(0?[92])(\s?)[987654321]\d{7}$/'
            ),
            'email_contacto_4' => 'nullable|email',
            'phone_contacto_4' =>  array(
                'nullable',
                'regex:/^(\+56)?(\s?)(0?[92])(\s?)[987654321]\d{7}$/'
            ),
            'email_contacto_5' => 'nullable|email',
            'phone_contacto_5' =>  array(
                'nullable',
                'regex:/^(\+56)?(\s?)(0?[92])(\s?)[987654321]\d{7}$/'
            ),
        ], [
            'email_contacto.email' => 'Por favor, ingresa una dirección de correo válida',
            'email_contacto_2.email' => 'Por favor, ingresa una dirección de correo válida',
            'email_contacto_3.email' => 'Por favor, ingresa una dirección de correo válida',
            'email_contacto_4.email' => 'Por favor, ingresa una dirección de correo válida',
            'email_contacto_5.email' => 'Por favor, ingresa una dirección de correo válida',
            'phone_contacto.regex' => 'Formato de Telefono: +56912345678',
            'phone_contacto_2.regex' => 'Formato de Telefono: +56912345678',
            'phone_contacto_3.regex' => 'Formato de Telefono: +56912345678',
            'phone_contacto_4.regex' => 'Formato de Telefono: +56912345678',
            'phone_contacto_5.regex' => 'Formato de Telefono: +56912345678',
        ]);
        $client = Client::find($id);
        $client->nombre             = (trim($request->input('nombre')));
        $client->direccion             = (trim($request->input('direccion')));
        $client->poblacion             = (trim($request->input('poblacion')));
        $client->nacional             = (trim($request->input('nacional')));
        $client->telefono              = (trim($request->input('telefono')));
        $client->codigo              = (trim($request->input('codigo')));
        $client->codigo_zona              = (trim($request->input('codigo_zona')));
        $client->clasificacion             = (trim($request->input('clasificacion')));
        $client->nombre_contacto  = (trim($request->input('nombre_contacto')) != '') ? $request->input('nombre_contacto') : $client->nombre_contacto;
        $client->cargo_contacto   = (trim($request->input('cargo_contacto')) != '') ? $request->input('cargo_contacto') : $client->cargo_contacto;
        $client->email_contacto   = (trim($request->input('email_contacto')) != '') ? $request->input('email_contacto') : $client->email_contacto;
        $client->phone_contacto   = (trim($request->input('phone_contacto')) != '') ? $request->input('phone_contacto') : $client->phone_contacto;
        $client->comuna_contacto   = (trim($request->input('comuna_contacto')) != '') ? $request->input('comuna_contacto') : $client->comuna_contacto;
        $client->direccion_contacto   = (trim($request->input('direccion_contacto')) != '') ? $request->input('direccion_contacto') : $client->direccion_contacto;
        $client->active_contacto           = (trim($request->input('active_contacto')) != '') ? $request->input('active_contacto') : 'inactivo';

        $client->nombre_contacto_2  = (trim($request->input('nombre_contacto_2')) != '') ? $request->input('nombre_contacto_2') : $client->nombre_contacto_2;
        $client->cargo_contacto_2   = (trim($request->input('cargo_contacto_2')) != '') ? $request->input('cargo_contacto_2') : $client->cargo_contacto_2;
        $client->email_contacto_2   = (trim($request->input('email_contacto_2')) != '') ? $request->input('email_contacto_2') : $client->email_contacto_2;
        $client->phone_contacto_2   = (trim($request->input('phone_contacto_2')) != '') ? $request->input('phone_contacto_2') : $client->phone_contacto_2;
        $client->comuna_contacto_2   = (trim($request->input('comuna_contacto_2')) != '') ? $request->input('comuna_contacto_2') : $client->comuna_contacto_2;
        $client->direccion_contacto_2   = (trim($request->input('direccion_contacto_2')) != '') ? $request->input('direccion_contacto_2') : $client->direccion_contacto_2;
        $client->active_contacto_2           = (trim($request->input('active_contacto_2')) != '') ? $request->input('active_contacto_2') : 'inactivo';

        $client->nombre_contacto_3  = (trim($request->input('nombre_contacto_3')) != '') ? $request->input('nombre_contacto_3') : $client->nombre_contacto_3;
        $client->cargo_contacto_3   = (trim($request->input('cargo_contacto_3')) != '') ? $request->input('cargo_contacto_3') : $client->cargo_contacto_3;
        $client->email_contacto_3   = (trim($request->input('email_contacto_3')) != '') ? $request->input('email_contacto_3') : $client->email_contacto_3;
        $client->phone_contacto_3   = (trim($request->input('phone_contacto_3')) != '') ? $request->input('phone_contacto_3') : $client->phone_contacto_3;
        $client->comuna_contacto_3   = (trim($request->input('comuna_contacto_3')) != '') ? $request->input('comuna_contacto_3') : $client->comuna_contacto_3;
        $client->direccion_contacto_3   = (trim($request->input('direccion_contacto_3')) != '') ? $request->input('direccion_contacto_3') : $client->direccion_contacto_3;

        $client->active_contacto_3           = (trim($request->input('active_contacto_3')) != '') ? $request->input('active_contacto_3') : 'inactivo';

        $client->nombre_contacto_4  = (trim($request->input('nombre_contacto_4')) != '') ? $request->input('nombre_contacto_4') : $client->nombre_contacto_4;
        $client->cargo_contacto_4   = (trim($request->input('cargo_contacto_4')) != '') ? $request->input('cargo_contacto_4') : $client->cargo_contacto_4;
        $client->email_contacto_4   = (trim($request->input('email_contacto_4')) != '') ? $request->input('email_contacto_4') : $client->email_contacto_4;
        $client->phone_contacto_4   = (trim($request->input('phone_contacto_4')) != '') ? $request->input('phone_contacto_4') : $client->phone_contacto_4;
        $client->comuna_contacto_4   = (trim($request->input('comuna_contacto_4')) != '') ? $request->input('comuna_contacto_4') : $client->comuna_contacto_4;
        $client->direccion_contacto_4   = (trim($request->input('direccion_contacto_4')) != '') ? $request->input('direccion_contacto_4') : $client->direccion_contacto_4;

        $client->active_contacto_4           = (trim($request->input('active_contacto_4')) != '') ? $request->input('active_contacto_4') : 'inactivo';

        $client->nombre_contacto_5  = (trim($request->input('nombre_contacto_5')) != '') ? $request->input('nombre_contacto_5') : $client->nombre_contacto_5;
        $client->cargo_contacto_5   = (trim($request->input('cargo_contacto_5')) != '') ? $request->input('cargo_contacto_5') : $client->cargo_contacto_5;
        $client->email_contacto_5   = (trim($request->input('email_contacto_5')) != '') ? $request->input('email_contacto_5') : $client->email_contacto_5;
        $client->phone_contacto_5   = (trim($request->input('phone_contacto_5')) != '') ? $request->input('phone_contacto_5') : $client->phone_contacto_5;
        $client->comuna_contacto_5   = (trim($request->input('comuna_contacto_5')) != '') ? $request->input('comuna_contacto_5') : $client->comuna_contacto_5;
        $client->direccion_contacto_5   = (trim($request->input('direccion_contacto_5')) != '') ? $request->input('direccion_contacto_5') : $client->direccion_contacto_5;
        $client->active_contacto_5           = (trim($request->input('active_contacto_5')) != '') ? $request->input('active_contacto_5') : 'inactivo';

        if (auth()->user()->isAdmin()) {
            $client->tipo_cliente = (trim($request->input('tipo_cliente')) != '') ? $request->input('tipo_cliente') : $client->tipo_cliente;
            $client->margen_minimo_vendedor_externo = (trim($request->input('margen_minimo_vendedor_externo')) != '') ? $request->input('margen_minimo_vendedor_externo') : $client->margen_minimo_vendedor_externo;
        }
        $client->save();
        return redirect()->route('mantenedores.clients.list')->with('success', 'Cliente  editado correctamente.');
    }

    public function update_installation()
    {

        $html='';

        $installation =  Installation::find($_GET['installation']);
        $installation->nombre               = (empty($_GET['nombre']))?null:$_GET['nombre'];
        $installation->client_id            = (empty($_GET['cliente']))?null:$_GET['cliente'];
        $installation->tipo_pallet          = ($_GET['tipo_pallet']=='')?null:$_GET['tipo_pallet'];
        $installation->altura_pallet        = (empty($_GET['altura_pallet']))?null:$_GET['altura_pallet'];
        $installation->sobresalir_carga     = ($_GET['sobresalir_carga']=='')?null:$_GET['sobresalir_carga'];
        $installation->bulto_zunchado       = ($_GET['bulto_zunchado']=='')?null:$_GET['bulto_zunchado'];
        $installation->formato_etiqueta     = (empty($_GET['formato_etiqueta']))?null:$_GET['formato_etiqueta'];
        $installation->etiquetas_pallet     = (empty($_GET['etiquetas_pallet']))?null:$_GET['etiquetas_pallet'];
        $installation->termocontraible      = ($_GET['termocontraible']=='')?null:$_GET['termocontraible'];
        $installation->fsc                  = ($_GET['fsc']=='')?null:$_GET['fsc'];
        $installation->pais_mercado_destino = ($_GET['pais_mercado_destino']=='')?null:$_GET['pais_mercado_destino'];
        $installation->certificado_calidad  = ($_GET['certificado_calidad']=='')?null:$_GET['certificado_calidad'];
        $installation->nombre_contacto      = (empty($_GET['nombre_contacto']))?null:$_GET['nombre_contacto'];
        $installation->cargo_contacto       = (empty($_GET['cargo_contacto']))?null:$_GET['cargo_contacto'];
        $installation->email_contacto       = (empty($_GET['email_contacto']))?null:$_GET['email_contacto'];
        $installation->phone_contacto       = (empty($_GET['phone_contacto']))?null:str_replace("*","+",$_GET['phone_contacto']);
        $installation->direccion_contacto   = (empty($_GET['direccion_contacto']))?null:$_GET['direccion_contacto'];
        $installation->comuna_contacto      = ($_GET['comuna_contacto']=='')?null:$_GET['comuna_contacto'];
        $installation->active_contacto      = ($_GET['active_contacto']=='')?'inactivo':$_GET['active_contacto'];
        $installation->nombre_contacto_2    = (empty($_GET['nombre_contacto_2']))?null:$_GET['nombre_contacto_2'];
        $installation->cargo_contacto_2     = (empty($_GET['cargo_contacto_2']))?null:$_GET['cargo_contacto_2'];
        $installation->email_contacto_2     = (empty($_GET['email_contacto_2']))?null:$_GET['email_contacto_2'];
        $installation->phone_contacto_2     = (empty($_GET['phone_contacto_2']))?null:str_replace("*","+",$_GET['phone_contacto_2']);
        $installation->direccion_contacto_2 = (empty($_GET['direccion_contacto_2']))?null:$_GET['direccion_contacto_2'];
        $installation->comuna_contacto_2    = ($_GET['comuna_contacto_2']=='')?null:$_GET['comuna_contacto_2'];
        $installation->active_contacto_2    = ($_GET['active_contacto_2']=='')?'inactivo':$_GET['active_contacto_2'];
        $installation->nombre_contacto_3    = (empty($_GET['nombre_contacto_3']))?null:$_GET['nombre_contacto_3'];
        $installation->cargo_contacto_3     = (empty($_GET['cargo_contacto_3']))?null:$_GET['cargo_contacto_3'];
        $installation->email_contacto_3     = (empty($_GET['email_contacto_3']))?null:$_GET['email_contacto_3'];
        $installation->phone_contacto_3     = (empty($_GET['phone_contacto_3']))?null:str_replace("*","+",$_GET['phone_contacto_3']);
        $installation->direccion_contacto_3 = (empty($_GET['direccion_contacto_3']))?null:$_GET['direccion_contacto_3'];
        $installation->comuna_contacto_3    = ($_GET['comuna_contacto_3']=='')?null:$_GET['comuna_contacto_3'];
        $installation->active_contacto_3    = ($_GET['active_contacto_3']=='')?'inactivo':$_GET['active_contacto_3'];
        $installation->nombre_contacto_4    = (empty($_GET['nombre_contacto_4']))?null:$_GET['nombre_contacto_4'];
        $installation->cargo_contacto_4     = (empty($_GET['cargo_contacto_4']))?null:$_GET['cargo_contacto_4'];
        $installation->email_contacto_4     = (empty($_GET['email_contacto_4']))?null:$_GET['email_contacto_4'];
        $installation->phone_contacto_4     = (empty($_GET['phone_contacto_4']))?null:str_replace("*","+",$_GET['phone_contacto_4']);
        $installation->direccion_contacto_4 = (empty($_GET['direccion_contacto_4']))?null:$_GET['direccion_contacto_4'];
        $installation->comuna_contacto_4    = ($_GET['comuna_contacto_4']=='')?null:$_GET['comuna_contacto_4'];
        $installation->active_contacto_4    = ($_GET['active_contacto_4']=='')?'inactivo':$_GET['active_contacto_4'];
        $installation->nombre_contacto_5    = (empty($_GET['nombre_contacto_5']))?null:$_GET['nombre_contacto_5'];
        $installation->cargo_contacto_5     = (empty($_GET['cargo_contacto_5']))?null:$_GET['cargo_contacto_5'];
        $installation->email_contacto_5     = (empty($_GET['email_contacto_5']))?null:$_GET['email_contacto_5'];
        $installation->phone_contacto_5     = (empty($_GET['phone_contacto_5']))?null:str_replace("*","+",$_GET['phone_contacto_5']);
        $installation->direccion_contacto_5 = (empty($_GET['direccion_contacto_5']))?null:$_GET['direccion_contacto_5'];
        $installation->comuna_contacto_5    = ($_GET['comuna_contacto_5']=='')?null:$_GET['comuna_contacto_5'];
        $installation->active_contacto_5    = ($_GET['active_contacto_5']=='')?'inactivo':$_GET['active_contacto_5'];
        $installation->active               = ($_GET['active']=='')?0:$_GET['active'];
        $installation->save();

        $installations = Installation::where('client_id',$_GET['cliente'])->get();

        foreach ($installations as $installation) {
            $html .='<tr style="background: #e4e6e4;">';
            $html .='<td>';
            $html .='<h3>&nbsp;&nbsp;'.$installation->nombre.'&nbsp;&nbsp;';
            $html .='<a herf="#" data-toggle="modal" data-target="#modal-editar-planta" data-editar="'.$installation->id.'">';
            $html .='<div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>';
            $html .='</a></h3>';
            $html .='</td>';
            $html .='</tr>';
            $html .='<tr>';
            $html .='<td>';
            $html .='<table class="table table-sm table-bordered" width="100%">';
            $html .='<thead>';
            $html .='<tr><th colspan="10"><h5><b>Datos Paletizado</b></h5></th></tr>';
            $html .='<tr>';
            $html .='<th style="width: 350px;"><b>Tipo Pallet</b></th>';
            $html .='<th style="width: 120px;"><b>Altura Pallet</b></th>';
            $html .='<th style="width: 120px;"><b>Sobresalir Carga</b></th>';
            $html .='<th style="width: 120px;"><b>Bulto Zunchado</b></th>';
            $html .='<th style="width: 120px;"><b>Formato Etiqueta</b></th>';
            $html .='<th style="width: 120px;"><b>Etiquetas Pallet</b></th>';
            $html .='<th style="width: 150px;"><b>Termocontraible</b></th>';
            $html .='<th style="width: 200px;"><b>Fsc</b></th>';
            $html .='<th style="width: 120px;"><b>Pais Mercado/Destino</b></th>';
            $html .='<th style="width: 120px;"><b>Certificado de Calidad</b></th>';
            $html .='</tr>';
            $html .='</thead>';
            $html .='<tbody>';
            $html .='<tr>';
            $html .=(is_null($installation->tipo_pallet))?'<td>N/A</td>':'<td>'.$installation->TipoPalleT->descripcion.'</td>';
            $html .=(is_null($installation->altura_pallet))?'<td>N/A</td>':'<td>'.$installation->altura_pallet.'</td>';
            $html .=($installation->sobresalir_carga==1)?'<td>SI</td>':'<td>NO</td>';
            $html .=($installation->bulto_zunchado==1)?'<td>SI</td>':'<td>NO</td>';
            $html .=(is_null($installation->formato_etiqueta))?'<td>N/A</td>':'<td>'.$installation->formato_etiqueta_pallet->descripcion.'</td>';
            $html .=(is_null($installation->etiquetas_pallet))?'<td>N/A</td>':'<td>'.$installation->etiquetas_pallet.'</td>';
            $html .=($installation->termocontraible==1)?'<td>SI</td>':'<td>NO</td>';
            $html .=(is_null($installation->fsc))?'<td>N/A</td>':'<td>'.$installation->Fsc->descripcion.'</td>';
            $html .=(is_null($installation->pais_mercado_destino))?'<td>N/A</td>':'<td>'.$installation->TargetMarket->name.'</td>';
            $html .=(is_null($installation->certificado_calidad))?'<td>N/A</td>':'<td>'.$installation->qa->descripcion.'</td>';
            $html .='</tr>';
            $html .='</tbody>';
            $html .='</table>';
            $html .='</td>';
            $html .='</tr>';
            $html .='<tr>';
            $html .='<td>';
            $html .='<table class="table table-sm table-bordered" width="100%">';
            $html .='<thead>';
            $html .='<tr><th colspan="8"><h5><b>Listado de Contactos</b></h5></th></tr>';
            $html .='<tr>';
            $html .='<th style="width: 150px;"><b>Descripción</b></th>';
            $html .='<th style="width: 150px;"><b>Nombre</b></th>';
            $html .='<th style="width: 150px;"><b>Cargo</b></th>';
            $html .='<th style="width: 150px;"><b>Correo</b></th>';
            $html .='<th style="width: 150px;"><b>Teléfono</b></th>';
            $html .='<th style="width: 150px;"><b>Comuna</b></th>';
            $html .='<th style="width: 300px;"><b>Dirección</b></th>';
            $html .='<th style="width: 100px;"><b>Estado</b></th>';
            $html .='</tr>';
            $html .='</thead>';
            $html .='<tbody>';
            $html .='<tr>';
            $html .='<td><b>Contacto 1</b></td>';
            $html .=(is_null($installation->nombre_contacto))?'<td>N/A</td>':'<td>'.$installation->nombre_contacto.'</td>';
            $html .=(is_null($installation->cargo_contacto))?'<td>N/A</td>':'<td>'.$installation->cargo_contacto.'</td>';
            $html .=(is_null($installation->email_contacto))?'<td>N/A</td>':'<td>'.$installation->email_contacto.'</td>';
            $html .=(is_null($installation->phone_contacto))?'<td>N/A</td>':'<td>'.$installation->phone_contacto.'</td>';
            $html .=(is_null($installation->comuna_contacto))?'<td>N/A</td>':'<td>'.$installation->Comuna->ciudad.'</td>';
            $html .=(is_null($installation->direccion_contacto))?'<td>N/A</td>':'<td>'.$installation->direccion_contacto.'</td>';
            $html .=(is_null($installation->active_contacto))?'<td>N/A</td>':'<td>'.$installation->active_contacto.'</td>';
            $html .='</tr>';
            $html .='<tr>';
            $html .='<td><b>Contacto 2</b></td>';
            $html .=(is_null($installation->nombre_contacto_2))?'<td>N/A</td>':'<td>'.$installation->nombre_contacto_2.'</td>';
            $html .=(is_null($installation->cargo_contacto_2))?'<td>N/A</td>':'<td>'.$installation->cargo_contacto_2.'</td>';
            $html .=(is_null($installation->email_contacto_2))?'<td>N/A</td>':'<td>'.$installation->email_contacto_2.'</td>';
            $html .=(is_null($installation->phone_contacto_2))?'<td>N/A</td>':'<td>'.$installation->phone_contacto_2.'</td>';
            $html .=(is_null($installation->comuna_contacto_2))?'<td>N/A</td>':'<td>'.$installation->Comuna_2->ciudad.'</td>';
            $html .=(is_null($installation->direccion_contacto_2))?'<td>N/A</td>':'<td>'.$installation->direccion_contacto_2.'</td>';
            $html .=(is_null($installation->active_contacto_2))?'<td>N/A</td>':'<td>'.$installation->active_contacto_2.'</td>';
            $html .='</tr>';
            $html .='<tr>';
            $html .='<td><b>Contacto 3</b></td>';
            $html .=(is_null($installation->nombre_contacto_3))?'<td>N/A</td>':'<td>'.$installation->nombre_contacto_3.'</td>';
            $html .=(is_null($installation->cargo_contacto_3))?'<td>N/A</td>':'<td>'.$installation->cargo_contacto_3.'</td>';
            $html .=(is_null($installation->email_contacto_3))?'<td>N/A</td>':'<td>'.$installation->email_contacto_3.'</td>';
            $html .=(is_null($installation->phone_contacto_3))?'<td>N/A</td>':'<td>'.$installation->phone_contacto_3.'</td>';
            $html .=(is_null($installation->comuna_contacto_3))?'<td>N/A</td>':'<td>'.$installation->Comuna_3->ciudad.'</td>';
            $html .=(is_null($installation->direccion_contacto_3))?'<td>N/A</td>':'<td>'.$installation->direccion_contacto_3.'</td>';
            $html .=(is_null($installation->active_contacto_3))?'<td>N/A</td>':'<td>'.$installation->active_contacto_3.'</td>';
            $html .='</tr>';
            $html .='<tr>';
            $html .='<td><b>Contacto 4</b></td>';
            $html .=(is_null($installation->nombre_contacto_4))?'<td>N/A</td>':'<td>'.$installation->nombre_contacto_4.'</td>';
            $html .=(is_null($installation->cargo_contacto_4))?'<td>N/A</td>':'<td>'.$installation->cargo_contacto_4.'</td>';
            $html .=(is_null($installation->email_contacto_4))?'<td>N/A</td>':'<td>'.$installation->email_contacto_4.'</td>';
            $html .=(is_null($installation->phone_contacto_4))?'<td>N/A</td>':'<td>'.$installation->phone_contacto_4.'</td>';
            $html .=(is_null($installation->comuna_contacto_4))?'<td>N/A</td>':'<td>'.$installation->Comuna_4->ciudad.'</td>';
            $html .=(is_null($installation->direccion_contacto_4))?'<td>N/A</td>':'<td>'.$installation->direccion_contacto_4.'</td>';
            $html .=(is_null($installation->active_contacto_4))?'<td>N/A</td>':'<td>'.$installation->active_contacto_4.'</td>';
            $html .='</tr>';
            $html .='<tr>';
            $html .='<td><b>Contacto 5</b></td>';
            $html .=(is_null($installation->nombre_contacto_5))?'<td>N/A</td>':'<td>'.$installation->nombre_contacto_5.'</td>';
            $html .=(is_null($installation->cargo_contacto_5))?'<td>N/A</td>':'<td>'.$installation->cargo_contacto_5.'</td>';
            $html .=(is_null($installation->email_contacto_5))?'<td>N/A</td>':'<td>'.$installation->email_contacto_5.'</td>';
            $html .=(is_null($installation->phone_contacto_5))?'<td>N/A</td>':'<td>'.$installation->phone_contacto_5.'</td>';
            $html .=(is_null($installation->comuna_contacto_5))?'<td>N/A</td>':'<td>'.$installation->Comuna_5->ciudad.'</td>';
            $html .=(is_null($installation->direccion_contacto_5))?'<td>N/A</td>':'<td>'.$installation->direccion_contacto_5.'</td>';
            $html .=(is_null($installation->active_contacto_5))?'<td>N/A</td>':'<td>'.$installation->active_contacto_5.'</td>';
            $html .='</tr>';
            $html .='</tbody>';
            $html .='</table>';
            $html .='</td>';
            $html .='</tr>';
        }
        //dd($html);
        return $html;

    }

    public function active($id)
    {
        Client::findOrFail($id)->update(['active' => 1]);
        return redirect()->route('mantenedores.clients.list')->with('success', 'Cliente activado correctamente.');
    }

    public function inactive($id)
    {
        Client::findOrFail($id)->update(['active' => 0]);
        return redirect()->route('mantenedores.clients.list')->with('success', 'Cliente inactivado correctamente.');
    }

    public function getContactosCliente()
    {
        // dd(request()->all());
        if (!empty($_GET['client_id']) && $_GET['client_id'] != "null") {
            if(!empty($_GET['instalacion_cliente']) && $_GET['instalacion_cliente'] != "null"){
                $cliente = Installation::find($_GET['instalacion_cliente']);
            }else{
                $cliente = Client::find($_GET['client_id']);
            }

            $contactos = [];
            if ($cliente->nombre_contacto != "" && $cliente->active_contacto == "activo") {
                $contactos[1] = $cliente->nombre_contacto;
            }
            for ($i = 2; $i <= 5; $i++) {
                if ($cliente["nombre_contacto_" . $i] != "" && $cliente["active_contacto_" . $i] == "activo") {
                    $contactos[$i] = $cliente["nombre_contacto_" . $i];
                }
            }
            // dd($contactos);
            $html = '<option value="" disabled selected>Seleccionar Opción</option>' . optionsSelectArrayfilterSimple($contactos, 'contacto_id');

            return $html;
        }
        return "";
    }

    public function getInstalacionesClienteCotiza()
    {
        // dd(request()->all());
        if (!empty($_GET['client_id']) && $_GET['client_id'] != "null") {
            $instalations = Installation::where('client_id',$_GET['client_id'])->where('active',1)->pluck('nombre', 'id')->toArray();

            //Obtener clasifcicacion del cliente
            $cliente = Client::find($_GET['client_id']);
            $cliente_clasificacion = ClasificacionCliente::where('id',$cliente->clasificacion)->where('active',1)->pluck('name', 'id')->toArray();

            /*$instalaciones = [];
            if ($cliente->nombre_contacto != "" && $cliente->active_contacto == "activo") {
                $contactos[1] = $cliente->nombre_contacto;
            }
            foreach($instalations as $installation){

            }
            for ($i = 2; $i <= 5; $i++) {
                if ($cliente["nombre_contacto_" . $i] != "" && $cliente["active_contacto_" . $i] == "activo") {
                    $contactos[$i] = $cliente["nombre_contacto_" . $i];
                }
            }*/
            // dd($contactos);
            $html = '<option value="" disabled selected>Seleccionar Opción</option>' . optionsSelectArrayfilterSimple($instalations, 'id');
            $html_clasificacion = optionsSelectArrayfilterSimple($cliente_clasificacion, 'id');
            return response()->json(['html' => $html,
                                     'clasificacion' => $cliente->clasificacion,
                                     'html_clasificacion' => $html_clasificacion]);
        }
        return "";
    }

    public function getInstalacionesCliente()
    {
        // dd(request()->all());
        if (!empty($_GET['client_id']) && $_GET['client_id'] != "null") {
            $instalations = Installation::where('client_id',$_GET['client_id'])->where('active',1)->pluck('nombre', 'id')->toArray();

            // var_dump($instalations[0]);
            /*$instalaciones = [];
            if ($cliente->nombre_contacto != "" && $cliente->active_contacto == "activo") {
                $contactos[1] = $cliente->nombre_contacto;
            }
            foreach($instalations as $installation){

            }
            for ($i = 2; $i <= 5; $i++) {
                if ($cliente["nombre_contacto_" . $i] != "" && $cliente["active_contacto_" . $i] == "activo") {
                    $contactos[$i] = $cliente["nombre_contacto_" . $i];
                }
            }*/
            // dd($contactos);
            $html = '<option value="" disabled selected>Seleccionar Opción</option>' . optionsSelectArrayfilterSimpleSelected($instalations, 'id');

            return $html;
        }
        return "";
    }

    public function getInformacionInstalacion()
    {
        // dd(request()->all());
        if (!empty($_GET['instalation_id']) && $_GET['instalation_id'] != "null") {
            $instalacion = Installation::find($_GET['instalation_id']);
            $contactos = [];
            if ($instalacion->nombre_contacto != "" && $instalacion->active_contacto == "activo") {
                $contactos[1] = $instalacion->nombre_contacto;
            }
            for ($i = 2; $i <= 5; $i++) {
                if ($instalacion["nombre_contacto_" . $i] != "" && $instalacion["active_contacto_" . $i] == "activo") {
                    $contactos[$i] = $instalacion["nombre_contacto_" . $i];
                }
            }
            // dd($contactos);
            $contactos = '<option value="" disabled selected>Seleccionar Opción</option>' . optionsSelectArrayfilterSimple($contactos, 'contacto_id');
            $informacion = ["contactos" => $contactos, "tipo_pallet" => $instalacion->tipo_pallet, "altura_pallet" => $instalacion->altura_pallet, "sobresalir_carga" => $instalacion->sobresalir_carga, "bulto_zunchado" =>  $instalacion->bulto_zunchado, "formato_etiqueta" =>  $instalacion->formato_etiqueta, "etiquetas_pallet" =>  $instalacion->etiquetas_pallet, "termocontraible" =>  $instalacion->termocontraible, "fsc" =>  $instalacion->fsc, "pais_mercado_destino" =>  $instalacion->pais_mercado_destino, "certificado_calidad" =>  $instalacion->certificado_calidad];

            return $informacion;
        }
        return "";
    }

    public function getDatosContacto()
    {
        // dd(request()->all());
        if (!empty($_GET['contactos_cliente']) && !empty($_GET['client_id'])) {
            $cliente = Client::find($_GET['client_id']);
            if ($_GET['contactos_cliente'] == 1) {
                $nombre = $cliente->nombre_contacto;
                $correo = $cliente->email_contacto;
                $telefono = $cliente->phone_contacto;
                $comuna = $cliente->comuna_contacto;
                $direccion = $cliente->direccion_contacto;
            } else {
                $nombre = $cliente["nombre_contacto_" . $_GET['contactos_cliente']];
                $correo = $cliente["email_contacto_" . $_GET['contactos_cliente']];
                $telefono = $cliente["phone_contacto_" . $_GET['contactos_cliente']];
                $comuna = $cliente["comuna_contacto_" . $_GET['contactos_cliente']];
                $direccion = $cliente["direccion_contacto_" . $_GET['contactos_cliente']];
            }
            $contacto = ["nombre_contacto" => $nombre, "email_contacto" => $correo, "telefono_contacto" => $telefono, "comuna_contacto" => $comuna, "direccion_contacto" => $direccion];
            return $contacto;
        }
        return "";
    }

    public function getDatosContactoInstalacion()
    {
        // dd(request()->all());
        if (!empty($_GET['contactos_cliente']) && !empty($_GET['instalation_id'])) {
            $instalacion = Installation::find($_GET['instalation_id']);
            if ($_GET['contactos_cliente'] == 1) {
                $nombre = $instalacion->nombre_contacto;
                $correo = $instalacion->email_contacto;
                $telefono = $instalacion->phone_contacto;
                $comuna = $instalacion->comuna_contacto;
                $direccion = $instalacion->direccion_contacto;
            } else {
                $nombre = $instalacion["nombre_contacto_" . $_GET['contactos_cliente']];
                $correo = $instalacion["email_contacto_" . $_GET['contactos_cliente']];
                $telefono = $instalacion["phone_contacto_" . $_GET['contactos_cliente']];
                $comuna = $instalacion["comuna_contacto_" . $_GET['contactos_cliente']];
                $direccion = $instalacion["direccion_contacto_" . $_GET['contactos_cliente']];
            }
            $contacto = ["nombre_contacto" => $nombre, "email_contacto" => $correo, "telefono_contacto" => $telefono, "comuna_contacto" => $comuna, "direccion_contacto" => $direccion];
            return $contacto;
        }
        return "";
    }

    public function store_indicacion()
    {

        $html='';

        $role=Role::where('id',Auth::user()->role_id)->first();
        $nombre_user= Auth::user()->nombre.' '.Auth::user()->apellido.'-'.$role->nombre;

        $indicacion = new IndicacionEspecial();
        $indicacion->client_id          = (empty($_GET['cliente']))?null:$_GET['cliente'];
        $indicacion->garantia_ect       = ($_GET['garantia_ect']=='')?null:$_GET['garantia_ect'];
        $indicacion->campo_1            = (empty($_GET['campo_1']))?null:$_GET['campo_1'];
        $indicacion->user_id_campo_1    = (empty($_GET['campo_1']))?null:Auth::user()->id;
        $indicacion->user_name_campo_1  = (empty($_GET['campo_1']))?null:$nombre_user;
        $indicacion->campo_2            = (empty($_GET['campo_2']))?null:$_GET['campo_2'];
        $indicacion->user_id_campo_2    = (empty($_GET['campo_2']))?null:Auth::user()->id;
        $indicacion->user_name_campo_2  = (empty($_GET['campo_2']))?null:$nombre_user;
        $indicacion->campo_3            = (empty($_GET['campo_3']))?null:$_GET['campo_3'];
        $indicacion->user_id_campo_3    = (empty($_GET['campo_3']))?null:Auth::user()->id;
        $indicacion->user_name_campo_3  = (empty($_GET['campo_3']))?null:$nombre_user;
        $indicacion->campo_4            = (empty($_GET['campo_4']))?null:$_GET['campo_4'];
        $indicacion->user_id_campo_4    = (empty($_GET['campo_4']))?null:Auth::user()->id;
        $indicacion->user_name_campo_4  = (empty($_GET['campo_4']))?null:$nombre_user;
        $indicacion->campo_5            = (empty($_GET['campo_5']))?null:$_GET['campo_5'];
        $indicacion->user_id_campo_5    = (empty($_GET['campo_5']))?null:Auth::user()->id;
        $indicacion->user_name_campo_5  = (empty($_GET['campo_5']))?null:$nombre_user;
        $indicacion->campo_6            = (empty($_GET['campo_6']))?null:$_GET['campo_6'];
        $indicacion->user_id_campo_6    = (empty($_GET['campo_6']))?null:Auth::user()->id;
        $indicacion->user_name_campo_6  = (empty($_GET['campo_6']))?null:$nombre_user;
        $indicacion->campo_7            = (empty($_GET['campo_7']))?null:$_GET['campo_7'];
        $indicacion->user_id_campo_7    = (empty($_GET['campo_7']))?null:Auth::user()->id;
        $indicacion->user_name_campo_7  = (empty($_GET['campo_7']))?null:$nombre_user;
        $indicacion->campo_8            = (empty($_GET['campo_8']))?null:$_GET['campo_8'];
        $indicacion->user_id_campo_8    = (empty($_GET['campo_8']))?null:Auth::user()->id;
        $indicacion->user_name_campo_8  = (empty($_GET['campo_8']))?null:$nombre_user;
        $indicacion->campo_9            = (empty($_GET['campo_9']))?null:$_GET['campo_9'];
        $indicacion->user_id_campo_9    = (empty($_GET['campo_9']))?null:Auth::user()->id;
        $indicacion->user_name_campo_9  = (empty($_GET['campo_9']))?null:$nombre_user;
        $indicacion->campo_10           = (empty($_GET['campo_10']))?null:$_GET['campo_10'];
        $indicacion->user_id_campo_10   = (empty($_GET['campo_10']))?null:Auth::user()->id;
        $indicacion->user_name_campo_10 = (empty($_GET['campo_10']))?null:$nombre_user;
        $indicacion->deleted            = 0;
        $indicacion->save();

        $indicaciones = IndicacionEspecial::where('client_id',$_GET['cliente'])->get();

        foreach ($indicaciones as $indicacion) {


            if(!is_null($indicacion->garantia_ect)){
                $html .='<tr>';
                $html .='<td><b>Garantía Ect:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->garantia_ect.'</td>';
                $html .='</tr>';
            }
            if(!is_null($indicacion->campo_1)){
                $html .='<tr>';
                $html .='<td><b>Indicación 1:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->campo_1.'. &nbsp;<b>'.$indicacion->user_name_campo_1.'</b></td>';
                $html .='</tr>';
            }
            if(!is_null($indicacion->campo_2)){
                $html .='<tr>';
                $html .='<td><b>Indicación 2:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->campo_2.'. &nbsp;<b>'.$indicacion->user_name_campo_2.'</b></td>';
                $html .='</tr>';
            }
            if(!is_null($indicacion->campo_3)){
                $html .='<tr>';
                $html .='<td><b>Indicación 3:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->campo_3.'. &nbsp;<b>'.$indicacion->user_name_campo_3.'</b></td>';
                $html .='</tr>';
            }
            if(!is_null($indicacion->campo_4)){
                $html .='<tr>';
                $html .='<td><b>Indicación 4:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->campo_4.'. &nbsp;<b>'.$indicacion->user_name_campo_4.'</b></td>';
                $html .='</tr>';
            }
            if(!is_null($indicacion->campo_5)){
                $html .='<tr>';
                $html .='<td><b>Indicación 5:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->campo_5.'. &nbsp;<b>'.$indicacion->user_name_campo_5.'</b></td>';
                $html .='</tr>';
            }
            if(!is_null($indicacion->campo_6)){
                $html .='<tr>';
                $html .='<td><b>Indicación 6:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->campo_6.'. &nbsp;<b>'.$indicacion->user_name_campo_6.'</b></td>';
                $html .='</tr>';
            }
            if(!is_null($indicacion->campo_7)){
                $html .='<tr>';
                $html .='<td><b>Indicación 7:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->campo_7.'. &nbsp;<b>'.$indicacion->user_name_campo_7.'</b></td>';
                $html .='</tr>';
            }
            if(!is_null($indicacion->campo_8)){
                $html .='<tr>';
                $html .='<td><b>Indicación 8:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->campo_8.'. &nbsp;<b>'.$indicacion->user_name_campo_8.'</b></td>';
                $html .='</tr>';
            }
            if(!is_null($indicacion->campo_9)){
                $html .='<tr>';
                $html .='<td><b>Indicación 9:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->campo_9.'. &nbsp;<b>'.$indicacion->user_name_campo_9.'</b></td>';
                $html .='</tr>';
            }
            if(!is_null($indicacion->campo_10)){
                $html .='<tr>';
                $html .='<td><b>Indicación 10: </b>&nbsp;'.$indicacion->campo_10.'. &nbsp;<b>'.$indicacion->user_name_campo_10.'</b></td>';
                $html .='</tr>';
            }

        }

        return $html;

    }

    public function edit_indicacion()
    {
        $indicacion = IndicacionEspecial::find($_GET['indicacion']);

        return $indicacion;
    }

    public function update_indicacion()
    {

        $html='';

        $role=Role::where('id',Auth::user()->role_id)->first();
        $nombre_user= Auth::user()->nombre.' '.Auth::user()->apellido.'-'.$role->nombre;

        $indicacion =  IndicacionEspecial::find($_GET['indicacion']);

        $indicacion->client_id     = (empty($_GET['cliente']))?null:$_GET['cliente'];
        $indicacion->garantia_ect  = ($_GET['garantia_ect']=='')?null:$_GET['garantia_ect'];

        /////Campo 1
        if(empty($_GET['campo_1'])){
            $indicacion->campo_1            = null;
            $indicacion->user_id_campo_1    = null;
            $indicacion->user_name_campo_1  = null;
        }else{
            if(is_null($indicacion->campo_1)){
                $indicacion->campo_1            = $_GET['campo_1'];
                $indicacion->user_id_campo_1    = Auth::user()->id;
                $indicacion->user_name_campo_1  = $nombre_user;
            }else{
                $valor_actual=trim($indicacion->campo_1);
                $valor_nuevo=trim($_GET['campo_1']);
                if(strcmp($valor_actual,$valor_nuevo)===0){

                }else{
                    $indicacion->campo_1            = $_GET['campo_1'];
                    $indicacion->user_id_campo_1    = Auth::user()->id;
                    $indicacion->user_name_campo_1  = $nombre_user;
                }
            }
        }

        /////Campo 2
        if(empty($_GET['campo_2'])){
            $indicacion->campo_2            = null;
            $indicacion->user_id_campo_2    = null;
            $indicacion->user_name_campo_2  = null;
        }else{
            if(is_null($indicacion->campo_2)){
                $indicacion->campo_2            = $_GET['campo_2'];
                $indicacion->user_id_campo_2    = Auth::user()->id;
                $indicacion->user_name_campo_2  = $nombre_user;
            }else{
                $valor_actual=trim($indicacion->campo_2);
                $valor_nuevo=trim($_GET['campo_2']);
                if(strcmp($valor_actual,$valor_nuevo)===0){

                }else{
                    $indicacion->campo_2            = $_GET['campo_2'];
                    $indicacion->user_id_campo_2    = Auth::user()->id;
                    $indicacion->user_name_campo_2  = $nombre_user;
                }
            }
        }

        /////Campo 3
        if(empty($_GET['campo_3'])){
            $indicacion->campo_3            = null;
            $indicacion->user_id_campo_3    = null;
            $indicacion->user_name_campo_3  = null;
        }else{
            if(is_null($indicacion->campo_3)){
                $indicacion->campo_3            = $_GET['campo_3'];
                $indicacion->user_id_campo_3    = Auth::user()->id;
                $indicacion->user_name_campo_3  = $nombre_user;
            }else{
                $valor_actual=trim($indicacion->campo_3);
                $valor_nuevo=trim($_GET['campo_3']);
                if(strcmp($valor_actual,$valor_nuevo)===0){

                }else{
                    $indicacion->campo_3            = $_GET['campo_3'];
                    $indicacion->user_id_campo_3    = Auth::user()->id;
                    $indicacion->user_name_campo_3  = $nombre_user;
                }
            }
        }

        /////Campo 4
        if(empty($_GET['campo_4'])){
            $indicacion->campo_4            = null;
            $indicacion->user_id_campo_4    = null;
            $indicacion->user_name_campo_4  = null;
        }else{
            if(is_null($indicacion->campo_4)){
                $indicacion->campo_4            = $_GET['campo_4'];
                $indicacion->user_id_campo_4    = Auth::user()->id;
                $indicacion->user_name_campo_4  = $nombre_user;
            }else{
                $valor_actual=trim($indicacion->campo_4);
                $valor_nuevo=trim($_GET['campo_4']);
                if(strcmp($valor_actual,$valor_nuevo)===0){

                }else{
                    $indicacion->campo_4            = $_GET['campo_4'];
                    $indicacion->user_id_campo_4    = Auth::user()->id;
                    $indicacion->user_name_campo_4  = $nombre_user;
                }
            }
        }

        /////Campo 5
        if(empty($_GET['campo_5'])){
            $indicacion->campo_5            = null;
            $indicacion->user_id_campo_5    = null;
            $indicacion->user_name_campo_5  = null;
        }else{
            if(is_null($indicacion->campo_5)){
                $indicacion->campo_5            = $_GET['campo_5'];
                $indicacion->user_id_campo_5    = Auth::user()->id;
                $indicacion->user_name_campo_5  = $nombre_user;
            }else{
                $valor_actual=trim($indicacion->campo_5);
                $valor_nuevo=trim($_GET['campo_5']);
                if(strcmp($valor_actual,$valor_nuevo)===0){

                }else{
                    $indicacion->campo_5            = $_GET['campo_5'];
                    $indicacion->user_id_campo_5    = Auth::user()->id;
                    $indicacion->user_name_campo_5  = $nombre_user;
                }
            }
        }

        /////Campo 6
        if(empty($_GET['campo_6'])){
            $indicacion->campo_6            = null;
            $indicacion->user_id_campo_6    = null;
            $indicacion->user_name_campo_6  = null;
        }else{
            if(is_null($indicacion->campo_6)){
                $indicacion->campo_6            = $_GET['campo_6'];
                $indicacion->user_id_campo_6    = Auth::user()->id;
                $indicacion->user_name_campo_6  = $nombre_user;
            }else{
                $valor_actual=trim($indicacion->campo_6);
                $valor_nuevo=trim($_GET['campo_6']);
                if(strcmp($valor_actual,$valor_nuevo)===0){

                }else{
                    $indicacion->campo_6            = $_GET['campo_6'];
                    $indicacion->user_id_campo_6    = Auth::user()->id;
                    $indicacion->user_name_campo_6  = $nombre_user;
                }
            }
        }

        /////Campo 7
        if(empty($_GET['campo_7'])){
            $indicacion->campo_7            = null;
            $indicacion->user_id_campo_7    = null;
            $indicacion->user_name_campo_7  = null;
        }else{
            if(is_null($indicacion->campo_7)){
                $indicacion->campo_7            = $_GET['campo_7'];
                $indicacion->user_id_campo_7    = Auth::user()->id;
                $indicacion->user_name_campo_7  = $nombre_user;
            }else{
                $valor_actual=trim($indicacion->campo_7);
                $valor_nuevo=trim($_GET['campo_7']);
                if(strcmp($valor_actual,$valor_nuevo)===0){

                }else{
                    $indicacion->campo_7            = $_GET['campo_7'];
                    $indicacion->user_id_campo_7    = Auth::user()->id;
                    $indicacion->user_name_campo_7  = $nombre_user;
                }
            }
        }

        /////Campo 8
        if(empty($_GET['campo_8'])){
            $indicacion->campo_8            = null;
            $indicacion->user_id_campo_8    = null;
            $indicacion->user_name_campo_8  = null;
        }else{
            if(is_null($indicacion->campo_8)){
                $indicacion->campo_8            = $_GET['campo_8'];
                $indicacion->user_id_campo_8    = Auth::user()->id;
                $indicacion->user_name_campo_8  = $nombre_user;
            }else{
                $valor_actual=trim($indicacion->campo_8);
                $valor_nuevo=trim($_GET['campo_8']);
                if(strcmp($valor_actual,$valor_nuevo)===0){

                }else{
                    $indicacion->campo_8            = $_GET['campo_8'];
                    $indicacion->user_id_campo_8    = Auth::user()->id;
                    $indicacion->user_name_campo_8  = $nombre_user;
                }
            }
        }

        /////Campo 9
        if(empty($_GET['campo_9'])){
            $indicacion->campo_9            = null;
            $indicacion->user_id_campo_9    = null;
            $indicacion->user_name_campo_9  = null;
        }else{
            if(is_null($indicacion->campo_9)){
                $indicacion->campo_9            = $_GET['campo_9'];
                $indicacion->user_id_campo_9    = Auth::user()->id;
                $indicacion->user_name_campo_9  = $nombre_user;
            }else{
                $valor_actual=trim($indicacion->campo_9);
                $valor_nuevo=trim($_GET['campo_9']);
                if(strcmp($valor_actual,$valor_nuevo)===0){

                }else{
                    $indicacion->campo_9            = $_GET['campo_9'];
                    $indicacion->user_id_campo_9    = Auth::user()->id;
                    $indicacion->user_name_campo_9  = $nombre_user;
                }
            }
        }

        /////Campo 10
        if(empty($_GET['campo_10'])){
            $indicacion->campo_10            = null;
            $indicacion->user_id_campo_10    = null;
            $indicacion->user_name_campo_10  = null;
        }else{
            if(is_null($indicacion->campo_10)){
                $indicacion->campo_10            = $_GET['campo_10'];
                $indicacion->user_id_campo_10    = Auth::user()->id;
                $indicacion->user_name_campo_10  = $nombre_user;
            }else{
                $valor_actual=trim($indicacion->campo_10);
                $valor_nuevo=trim($_GET['campo_10']);
                if(strcmp($valor_actual,$valor_nuevo)===0){

                }else{
                    $indicacion->campo_10            = $_GET['campo_10'];
                    $indicacion->user_id_campo_10    = Auth::user()->id;
                    $indicacion->user_name_campo_10  = $nombre_user;
                }
            }
        }
        /*$indicacion->campo_2       = (empty($_GET['campo_2']))?null:$_GET['campo_2'];
        $indicacion->campo_3       = (empty($_GET['campo_3']))?null:$_GET['campo_3'];
        $indicacion->campo_4       = (empty($_GET['campo_4']))?null:$_GET['campo_4'];
        $indicacion->campo_5       = (empty($_GET['campo_5']))?null:$_GET['campo_5'];
        $indicacion->campo_6       = (empty($_GET['campo_6']))?null:$_GET['campo_6'];
        $indicacion->campo_7       = (empty($_GET['campo_7']))?null:$_GET['campo_7'];
        $indicacion->campo_8       = (empty($_GET['campo_8']))?null:$_GET['campo_8'];
        $indicacion->campo_9       = (empty($_GET['campo_9']))?null:$_GET['campo_9'];
        $indicacion->campo_10      = (empty($_GET['campo_10']))?null:$_GET['campo_10'];
        $indicacion->deleted       = 0;*/
        $indicacion->save();

        $indicaciones = IndicacionEspecial::where('client_id',$_GET['cliente'])->get();

        foreach ($indicaciones as $indicacion) {


            if(!is_null($indicacion->garantia_ect)){
                $html .='<tr>';
                $html .='<td><b>Garantía Ect:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->garantia_ect.'</td>';
                $html .='</tr>';
            }
            if(!is_null($indicacion->campo_1)){
                $html .='<tr>';
                $html .='<td><b>Indicación 1:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->campo_1.'. &nbsp;<b>'.$indicacion->user_name_campo_1.'</b></td>';
                $html .='</tr>';
            }
            if(!is_null($indicacion->campo_2)){
                $html .='<tr>';
                $html .='<td><b>Indicación 2:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->campo_2.'. &nbsp;<b>'.$indicacion->user_name_campo_2.'</b></td>';
                $html .='</tr>';
            }
            if(!is_null($indicacion->campo_3)){
                $html .='<tr>';
                $html .='<td><b>Indicación 3:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->campo_3.'. &nbsp;<b>'.$indicacion->user_name_campo_3.'</b></td>';
                $html .='</tr>';
            }
            if(!is_null($indicacion->campo_4)){
                $html .='<tr>';
                $html .='<td><b>Indicación 4:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->campo_4.'. &nbsp;<b>'.$indicacion->user_name_campo_4.'</b></td>';
                $html .='</tr>';
            }
            if(!is_null($indicacion->campo_5)){
                $html .='<tr>';
                $html .='<td><b>Indicación 5:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->campo_5.'. &nbsp;<b>'.$indicacion->user_name_campo_5.'</b></td>';
                $html .='</tr>';
            }
            if(!is_null($indicacion->campo_6)){
                $html .='<tr>';
                $html .='<td><b>Indicación 6:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->campo_6.'. &nbsp;<b>'.$indicacion->user_name_campo_6.'</b></td>';
                $html .='</tr>';
            }
            if(!is_null($indicacion->campo_7)){
                $html .='<tr>';
                $html .='<td><b>Indicación 7:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->campo_7.'. &nbsp;<b>'.$indicacion->user_name_campo_7.'</b></td>';
                $html .='</tr>';
            }
            if(!is_null($indicacion->campo_8)){
                $html .='<tr>';
                $html .='<td><b>Indicación 8:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->campo_8.'. &nbsp;<b>'.$indicacion->user_name_campo_8.'</b></td>';
                $html .='</tr>';
            }
            if(!is_null($indicacion->campo_9)){
                $html .='<tr>';
                $html .='<td><b>Indicación 9:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->campo_9.'. &nbsp;<b>'.$indicacion->user_name_campo_9.'</b></td>';
                $html .='</tr>';
            }
            if(!is_null($indicacion->campo_10)){
                $html .='<tr>';
                $html .='<td><b>Indicación 10: </b>&nbsp;'.$indicacion->campo_10.'. &nbsp;<b>'.$indicacion->user_name_campo_10.'</b></td>';
                $html .='</tr>';
            }
        }
        //dd($html);
        return $html;

    }

    public function getIndicacionesEspeciales()
    {
        $html='';

        if (!empty($_GET['client_id']) && $_GET['client_id'] != "null") {
            $indicaciones = IndicacionEspecial::where('client_id',$_GET['client_id'])->where('deleted',0)->get();

            foreach ($indicaciones as $indicacion) {

                if(!is_null($indicacion->garantia_ect)){
                    $html .='<tr>';
                    $html .='<td><b>Garantía Ect:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->garantia_ect.'</td>';
                    $html .='</tr>';
                }
                if(!is_null($indicacion->campo_1)){
                    $html .='<tr>';
                    $html .='<td><b>Indicación 1:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->campo_1.'. &nbsp;<b>'.$indicacion->user_name_campo_1.'</b></td>';
                    $html .='</tr>';
                }
                if(!is_null($indicacion->campo_2)){
                    $html .='<tr>';
                    $html .='<td><b>Indicación 2:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->campo_2.'. &nbsp;<b>'.$indicacion->user_name_campo_2.'</b></td>';
                    $html .='</tr>';
                }
                if(!is_null($indicacion->campo_3)){
                    $html .='<tr>';
                    $html .='<td><b>Indicación 3:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->campo_3.'. &nbsp;<b>'.$indicacion->user_name_campo_3.'</b></td>';
                    $html .='</tr>';
                }
                if(!is_null($indicacion->campo_4)){
                    $html .='<tr>';
                    $html .='<td><b>Indicación 4:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->campo_4.'. &nbsp;<b>'.$indicacion->user_name_campo_4.'</b></td>';
                    $html .='</tr>';
                }
                if(!is_null($indicacion->campo_5)){
                    $html .='<tr>';
                    $html .='<td><b>Indicación 5:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->campo_5.'. &nbsp;<b>'.$indicacion->user_name_campo_5.'</b></td>';
                    $html .='</tr>';
                }
                if(!is_null($indicacion->campo_6)){
                    $html .='<tr>';
                    $html .='<td><b>Indicación 6:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->campo_6.'. &nbsp;<b>'.$indicacion->user_name_campo_6.'</b></td>';
                    $html .='</tr>';
                }
                if(!is_null($indicacion->campo_7)){
                    $html .='<tr>';
                    $html .='<td><b>Indicación 7:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->campo_7.'. &nbsp;<b>'.$indicacion->user_name_campo_7.'</b></td>';
                    $html .='</tr>';
                }
                if(!is_null($indicacion->campo_8)){
                    $html .='<tr>';
                    $html .='<td><b>Indicación 8:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->campo_8.'. &nbsp;<b>'.$indicacion->user_name_campo_8.'</b></td>';
                    $html .='</tr>';
                }
                if(!is_null($indicacion->campo_9)){
                    $html .='<tr>';
                    $html .='<td><b>Indicación 9:</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$indicacion->campo_9.'. &nbsp;<b>'.$indicacion->user_name_campo_9.'</b></td>';
                    $html .='</tr>';
                }
                if(!is_null($indicacion->campo_10)){
                    $html .='<tr>';
                    $html .='<td><b>Indicación 10: </b>&nbsp;'.$indicacion->campo_10.'. &nbsp;<b>'.$indicacion->user_name_campo_10.'</b></td>';
                    $html .='</tr>';
                }
            }

            return $html;
        }
        return "";
    }
}
