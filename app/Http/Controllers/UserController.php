<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Constants;
use App\DetalleCotizacion;
use App\Material;
use App\Role;
use App\WorkOrder;
use App\Client;
use App\SalaCorte;
use DateInterval;
use Illuminate\Support\Facades\Validator;
use Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //filtros:
        $users_filter = User::where('role_id', '<>', Constants::Admin)->get();
        $profiles = Role::where('id', '!=', Constants::Admin)->pluck('nombre', 'id')->toArray();


        //filters:
        $query = User::query();
        if (!is_null(request()->query('id'))) {
            $query = $query->whereIn('id', request()->query('id'));
        }
        if (!is_null(request()->query('role_id'))) {
            $query = $query->whereIn('role_id', request()->query('role_id'));
        }
        if (!is_null(request()->query('active'))) {
            $query = $query->whereIn('active', request()->query('active'));
        }
        /*order columnns table*/
        $orderby = trim(request()->query('orderby'));
        $sorted  = trim(request()->query('sorted'));
        $orderby = in_array($orderby, ['rut', 'nombre', 'role_id']) ? $orderby : 'nombre';
        $sorted  = in_array($sorted, ['ASC', 'DESC']) ? $sorted : 'ASC';
        $users = $query->with('role')->where('role_id', '<>', Constants::Admin)->orderBy($orderby, $sorted)->paginate(20);
        // \Debugbar::info($users);
        return view('users.index', compact('users', 'users_filter', 'profiles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */ 
    public function create()
    {
        $profiles = Role::where('id', '!=', Constants::Admin)->pluck('nombre', 'id')->toArray();
        $salas_cortes=SalaCorte::where('deleted',0)->pluck('nombre', 'id')->toArray();
        $clientes=Client::where('active',1)->pluck('nombre', 'id')->toArray();
        $jefesVenta = User::where('role_id', Constants::JefeVenta)->get()->map(function ($user) {
            return [
                'id'    => $user->id,
                'nombre'  => $user->fullname
            ];
        })
            ->pluck('nombre', 'id')->toArray();

        $vendedores = User::where('role_id', Constants::Vendedor)->get()->map(function ($user) {
                return [
                    'id'    => $user->id,
                    'nombre'  => $user->fullname
                ];
            })
                ->pluck('nombre', 'id')->toArray();

        return view('users.create', compact('profiles', 'jefesVenta','salas_cortes','clientes','vendedores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'rut' => 'required|regex:/^0*(\d{1,3}(\.?\d{3})*)\-?([\dkK])$/|unique:users,rut',
            'nombre' => 'required',
            'apellido' => 'required',
            'email' => 'required|ends_with:cmpc.com,cmpc.cl|email|unique:users,email',
            'password' => 'required|confirmed|min:4|max:50',
            'role_id' => 'required',
            'jefe_id' => 'required_if:role_id,4',
            'password' => 'required|min:10|regex:/^(?=[\040-\176]*?[A-Z])(?=[\040-\176]*?[a-z])(?=[\040-\176]*?[0-9])(?=[\040-\176]*?[#?!@$%^&*-_])[\040-\176]{8,72}$/|confirmed',
        ], [
            'password.regex' => 'Contraseña debe tener mínimo 10 digitos, al menos una mayuscula, minuscula, número y carac. especial. (#?!@$%^&*-_)'
        ]);
        // dd(request()->all());
        $request->request->remove('password_confirmation');
        $request['password'] = bcrypt($request->input('password'));
        $user = (new User)->fill($request->all()); //sustituye a User::create($request->all());
        $user->save();

        $user_update = User::find($user->id);
        $user_update->fecha_cambio_password = date("Y-m-d");
        $user_update->save();

        return redirect()->route('mantenedores.users.list')->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    { }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $user->password = "";
        $profiles = Role::where('id', '!=', Constants::Admin)->pluck('nombre', 'id')->toArray();
        $salas_cortes=SalaCorte::where('deleted',0)->pluck('nombre', 'id')->toArray();
        $clientes=Client::where('active',1)->pluck('nombre', 'id')->toArray();
        $jefesVenta = User::where('role_id', Constants::JefeVenta)->get()->map(function ($user) {
            return [
                'id'    => $user->id,
                'nombre'  => $user->fullname
            ];
        })
            ->pluck('nombre', 'id')->toArray();

        $vendedores = User::where('role_id', Constants::Vendedor)->get()->map(function ($user) {
            return [
                'id'    => $user->id,
                'nombre'  => $user->fullname
            ];
        })
            ->pluck('nombre', 'id')->toArray();
        return view('users.edit', compact('user', 'profiles', 'jefesVenta', 'salas_cortes','clientes','vendedores'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            // 'rut' => 'required|regex:/^0*(\d{1,3}(\.?\d{3})*)\-?([\dkK])$/|unique:users,rut',
            'nombre' => 'required',
            'apellido' => 'required',
            'email' => 'required|ends_with:cmpc.com,cmpc.cl|email|unique:users,email,' . $id,
            'role_id' => 'required',
            'jefe_id' => 'required_if:role_id,4',
            'password' => 'nullable|regex:/^(?=[\040-\176]*?[A-Z])(?=[\040-\176]*?[a-z])(?=[\040-\176]*?[0-9])(?=[\040-\176]*?[#?!@$%^&*-_])[\040-\176]{8,72}$/|confirmed',
        ], [
            'password.regex' => 'Contraseña debe tener mínimo 10 digitos, al menos una mayuscula, minuscula, número y carac. especial. (#?!@$%^&*-_)'
        ]);
        $request->request->remove('password_confirmation');
        if (!trim($request->input('password')) == '') {
            $request['password'] = bcrypt($request->input('password'));
        }
        $user = User::find($id);
        $user->nombre           = (trim($request->input('nombre')) != '') ? $request->input('nombre') : $user->nombre;
        $user->apellido         = (trim($request->input('apellido')) != '') ? $request->input('apellido') : $user->apellido;
        $user->rut              = (trim($request->input('rut')) != '') ? $request->input('rut') : $user->rut;
        $user->password         = (trim($request->input('password')) != '') ? $request->input('password') : $user->password;
        $user->email            = (trim($request->input('email')) != '') ? $request->input('email') : $user->email;
        $user->role_id          = (trim($request->input('role_id')) != '') ? $request->input('role_id') : $user->role_id;
        $user->jefe_id          = (trim($request->input('jefe_id')) != '') ? $request->input('jefe_id') : $user->jefe_id;
        $user->nombre_sap       = (trim($request->input('nombre_sap')) != '') ? $request->input('nombre_sap') : $user->nombre_sap;
        $user->sala_corte_id    = (trim($request->input('sala_corte_id')) != '') ? $request->input('sala_corte_id') : $user->sala_corte_id;
        $user->cliente_id       = (trim($request->input('cliente_id')) != '') ? $request->input('cliente_id') : $user->cliente_id;
        $user->responsable_id       = (trim($request->input('responsable_id')) != '') ? $request->input('responsable_id') : $user->responsable_id;
        $user->fecha_cambio_password = (trim($request->input('password')) != '') ? date("Y-m-d") : $user->fecha_cambio_password;
        $user->save();
        return redirect()->route('mantenedores.users.list')->with('success', 'Usuario  editado correctamente.');
    }

    public function editarContraseña($id)
    {
        $user = User::find($id);
        $user->password = "";
        $profiles = Role::where('id', '!=', Constants::Admin)->pluck('nombre', 'id')->toArray();
        return view('users.editar-contraseña', compact('user', 'profiles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizarContraseña(Request $request, $id)
    {
        // dd(request()->all());
        $request->validate([
            // 'rut' => 'required|regex:/^0*(\d{1,3}(\.?\d{3})*)\-?([\dkK])$/|unique:users,rut',
            'password' => 'required|min:10|regex:/^(?=[\040-\176]*?[A-Z])(?=[\040-\176]*?[a-z])(?=[\040-\176]*?[0-9])(?=[\040-\176]*?[#?!@$%^&*-_])[\040-\176]{8,72}$/|confirmed',
        ], [
            'password.regex' => 'Contraseña debe tener mínimo 10 digitos, al menos una mayuscula, minuscula, número y carac. especial. (#?!@$%^&*-_)'
        ]);
        $request->request->remove('password_confirmation');
        if (!trim($request->input('password')) == '') {
            $request['password'] = bcrypt($request->input('password'));
        }
        $user = User::find($id);
        $user->password         = (trim($request->input('password')) != '') ? $request->input('password') : $user->password;
        $user->fecha_cambio_password = date("Y-m-d");
        $user->save();
        return redirect()->route('Ots')->with('success', 'Contraseña  editada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function active($id)
    {
        User::findOrFail($id)->update(['active' => 1]);
        return redirect()->route('mantenedores.users.list')->with('success', 'Usuario activado correctamente.');
    }

    public function inactive($id)
    {
        User::findOrFail($id)->update(['active' => 0]);
        return redirect()->route('mantenedores.users.list')->with('success', 'Usuario inactivado correctamente.');
    }

    public function cargaUsersForm()
    {
        // dd("asd");
        return view('users.masive');
    }

    public function importUsers(Request $request)
    {
        $validator = Validator::make(
            [
                'archivo'      => $request->archivo,
                'extension' => strtolower($request->archivo->getClientOriginalExtension()),
            ],
            [
                'archivo'          => 'required',
                'extension'      => 'required|in:xlsx,xls,csv',
            ]

        );

        $path = $request->file('archivo')->getRealPath();
        $data = Excel::load($path, false, 'ISO-8859-1')->get();
        if ($data->count()) {
            foreach ($data as $key => $row) {
                // dd($row);
                $email = false;
                $rut = false;
                if (filter_var(trim($row->email), FILTER_VALIDATE_EMAIL)) {
                    $email = true;
                }
                if (strlen($row->rut) <= 10) {
                    $rut = true;
                }
                if ($row->nombre && $row->apellido && $rut &&  $row->role && $email) {
                    // dd($row->rut);
                    // $select_planta = Planta::where('fila', $value->fila)->where('hilera', $value->hilera)->where('cuartel_id', $request->get('cuartel_id'))->first();
                    $user = User::where('rut', $row->rut)->first();
                    // dd($user);
                    if ($user) {
                        $user->linea = $key + 2;
                        $usersDuplicados[] = $user;
                    } else {

                        $rol = "";
                        switch (trim($row->role)) {
                            case "Administrador":
                                $rol = 1;
                                break;
                            case 'Subgerente':
                                $rol = 2;
                                break;
                            case 'Supervisor':
                                $rol = 3;
                                break;
                            case 'Ejecutivo':
                                $rol = 4;
                                break;
                            default:
                                $rol = 0;
                        }
                        if ($rol == 0) {
                            $usersInvalidos[] = $key + 2;
                        } else {


                            $user = new User([
                                'nombre'             => trim($row->nombre),
                                'apellido'        => trim($row->apellido),
                                'rut'              => trim($row->rut),
                                'password'         => bcrypt(str_replace("-", "", trim($row->rut))),
                                'email'            => trim($row->email),
                                'role_id'             => $rol,
                                'active' => 'activo',
                            ]);
                            $user->save();
                            $user->linea = $key + 2;
                            $users[] = $user;
                        }
                    }
                } else {
                    // dd($row);
                    $usersInvalidos[] = $key + 2;
                }
            }
        }

        $exito = null;
        $failure = null;
        $error = null;
        $users_ingresados = [];
        $users_duplicados = [];
        $users_error = [];

        if (isset($users)) {
            $exito = 'Se ingresaron los siguientes users:';
            $users_ingresados = $users;
            // $plantas_ingresadas = Planta::whereIn('id', $ids)->with('status')->paginate(10);
        }
        if (isset($usersDuplicados)) {
            $users_duplicados = $usersDuplicados;
            $failure = 'Los siguientes users se encontraban ingresadas:';
        }
        if (isset($usersInvalidos)) {
            $users_error = $usersInvalidos;
            $error = 'Los siguientes users no poseen rut o nombre';
        }
        return view('users.masive', compact('exito', 'failure', 'error',  'users_ingresados', 'users_duplicados', 'users_error'));
    }

    public function getUsersByArea()
    {
        // dd(request()->all());
        if (!empty($_GET['area_id'])) {
            $role = [];
            switch ($_GET['area_id']) {
                case '1':
                    $role = [3, 4];
                    break;
                case '2':
                    $role = [5, 6];
                    break;
                case '3':
                    $role = [7, 8];
                    break;
                case '4':
                    $role = [9, 10];
                    break;
                case '5':
                    $role = [9, 10];
                    break;
                case '6':
                    $role = [13, 14];
                    break;
                default:
                    # code...
                    break;
            }
            // return $equipo_id;
            $users = User::where('active', 1)->whereIn('role_id', $role)->select(DB::raw('id, CONCAT(COALESCE(nombre,""), " ", COALESCE(apellido,"")) AS nombre'))->pluck('nombre', 'id')->toArray();

            $html = optionsSelectArrayfilterSimple($users, 'id');

            return $html;
        }
        return "";
    }

    public function logearUsuario($id)
    {
        // dd($id, request());
        $user = User::find($id);
        Auth::login($user);
        return redirect()->route("home");
    }

    public function test()
    {

        // $ots = WorkOrder::select('id', 'rmt', 'unidad_medida_bct')->whereNotNull("rmt")->whereNotNull("unidad_medida_bct")->get();
        // dd($ots);


        // foreach ($ots as $ot) {
        //     if ($ot->unidad_medida_bct == "0") {
        //         $ot->bct_min_lb = $ot->rmt;
        //         $ot->bct_min_kg = (int) ($ot->rmt * 0.454);
        //     } elseif ($ot->unidad_medida_bct == "1") {
        //         $ot->bct_min_lb = (int) ($ot->rmt * 2.205);
        //         $ot->bct_min_kg = $ot->rmt;
        //     }
        //     // dd($ot);
        //     $ot->save();
        //     dd($ots);
        // }


        // $materiales = Material::select('id', 'rmt', 'unidad_medida_bct')->whereNotNull("rmt")->whereNotNull("unidad_medida_bct")->get();
        // // dd($materiales);


        // foreach ($materiales as $material) {
        //     if ($material->unidad_medida_bct == "0" && is_numeric($material->rmt)) {
        //         $material->bct_min_lb = $material->rmt;
        //         $material->bct_min_kg = (int) ($material->rmt * 0.454);
        //     } elseif ($material->unidad_medida_bct == "1" && is_numeric($material->rmt)) {
        //         $material->bct_min_lb = (int) ($material->rmt * 2.205);
        //         $material->bct_min_kg = $material->rmt;
        //     }
        //     // dd($material);
        //     $material->save();
        // }
        // dd("FIN", $materiales);
        // $detalles = DetalleCotizacion::select('id', 'bct', 'unidad_medida_bct')->whereNotNull("bct")->whereNotNull("unidad_medida_bct")->get();
        // dd($detalles);
        // foreach ($detalles as $detalle) {
        //     if ($detalle->unidad_medida_bct == "0") {
        //         $detalle->bct_min_lb = $detalle->bct;
        //         $detalle->bct_min_kg = (int) ($detalle->bct * 0.454);
        //     } elseif ($detalle->unidad_medida_bct == "1") {
        //         $detalle->bct_min_lb = (int) ($detalle->bct * 2.205);
        //         $detalle->bct_min_kg = $detalle->bct;
        //     }
        //     // dd($detalle);
        //     $detalle->save();
        // }
        // dd($detalles);

        // get_working_hours('2020-05-01 08:00:00', '2020-05-08 12:00:00'); //Saturday: 0 hrs
        // get_working_hours('2020-05-09 08:00:00', '2020-05-09 21:00:00'); //Monday: 11 hrs
        // get_working_hours('2020-05-10 10:00:00', '2020-05-10 19:00:00'); //Monday: 9 hrs
        // get_working_hours('2020-05-07 09:00:00', '2020-05-07 18:00:00'); //fri-mon: 2 hrs
        // get_working_hours('2020-05-06 09:00:00', '2020-05-06 11:00:00'); //sat-mon: 1 hrs
        // get_working_hours('2020-05-05 09:12:01', '2020-05-08 16:07:03'); //fri-sun: 1 hrs


        // return get_working_hours('2020-06-09 09:00:00', '2020-06-09 17:00:00');
        // return get_working_hours('2020-06-09 17:44:00', '2020-06-19 08:16:00');
        // return get_working_hours('2020-06-09 17:44:00', '2020-06-13 08:50:45');

        return get_working_hours('2020-12-23 22:32:04', '2021-01-04 10:50:45');
        // return push_notification("hola richard", "pasame la app ahi pa probar los push", 1, "djjbEk7wiok:APA91bEVX0fI-_KtX-fcQzhzMFlh2zqbQEGeS6RUwPsqFiXJYNeGcClUQmccAzJICoTH57Whzw8QVg2f9Ote0KjntUzNm8H1whelK2GERTJ5H8fUl8sxHpqVS-8unDNwIgC5n9oxGoKL");


        // // ALGORITMO PARA RECALCULAR LOS TIEMPOS DE LAS GESTIONES
        // $ots = WorkOrder::with(['gestiones' => function ($query) {
        //     $query->where('management_type_id', 1);
        //     $query->orderBy("managements.id", "asc");
        // }])->whereMonth('created_at', '12')->get();
        // // $ots = WorkOrder::join("managements", "work_orders.id", '=', "managements.work_order_id")->get();
        // // dd($ots);
        // $counter = 0;
        // foreach ($ots as $ot) {
        //     // dump($ot->id);
        //     // if ($counter < 3) {
        //     //     // dd($ot);
        //     //     $counter++;
        //     //     continue;
        //     // }
        //     // ordenar gestiones por id
        //     $ot->gestiones = $ot->gestiones->sortBy('id');
        //     // dump($ot->gestiones);
        //     // La fecha inicial comienza con la creacion de la ot
        //     $fecha_inicial = $ot->created_at;
        //     foreach ($ot->gestiones as $gestion) {
        //         // la fecha final a comparar seria la de la creacion de la gestion
        //         $fecha_final = $gestion->created_at;
        //         // Solo recalculamos gestiones que no tengan duracion actual = 0 
        //         if ($gestion->duracion_segundos != 0) {
        //             // dump("--------------------------------------------");
        //             // dump("Fecha inicial: " . $fecha_inicial, "Fecha Final: " . $fecha_final);
        //             // dump("Calculo Anterior Seg: " . $gestion->duracion_segundos, "Calculo Nuevo Seg: " . (get_working_hours($fecha_inicial, $fecha_final) * 3600));
        //             // dump("Calculo Anterior Hora: " . $gestion->duracion_segundos / 3600, "Calculo Nuevo Hora: " . (get_working_hours($fecha_inicial, $fecha_final)));
        //             $gestion->duracion_segundos = (int) (get_working_hours($fecha_inicial, $fecha_final) * 3600);
        //             $gestion->save();
        //         }
        //         // Al finalizar el calculo la fecha final de esta gestion sera la inicial de la proxima gestion
        //         $fecha_inicial = $fecha_final;
        //     }
        //     // dd($ot->gestiones);
        // }
        // dd($ots);
    }

    public function getTiposVendedores()
    {
        // dd(request()->all());
        if (!empty($_GET['tipo_vendedor']) && $_GET['tipo_vendedor'] != "null") {
            //$instalations = Installation::where('client_id',$_GET['client_id'])->where('active',1)->pluck('nombre', 'id')->toArray();
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
            if($_GET['tipo_vendedor']==1){
                $vendedores = User::where('active', 1)->whereIn('role_id', [3, 4, 19])->get();
                $vendedores->map(function ($vendedor) {
                    $vendedor->vendedor_id = $vendedor->id;
                });
            }elseif ($_GET['tipo_vendedor']==4) {
                $vendedores = User::where('active', 1)->whereIn('role_id', [3, 4])->get();
                $vendedores->map(function ($vendedor) {
                    $vendedor->vendedor_id = $vendedor->id;
                });
            }elseif ($_GET['tipo_vendedor']==19) {
                $vendedores = User::where('active', 1)->whereIn('role_id', [19])->get();
                $vendedores->map(function ($vendedor) {
                    $vendedor->vendedor_id = $vendedor->id;
                });
            }
            $html = optionsSelectObjetfilterMultiple($vendedores,'vendedor_id',['nombre','apellido'],' ');
            return $html;
        }
        return "";
    }    
}
