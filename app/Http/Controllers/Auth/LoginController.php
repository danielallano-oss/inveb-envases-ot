<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\User;
use App\Usuario;
use Illuminate\Support\Str;
use App\PasswordSecurity;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\RestablecerContraseña;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(\Illuminate\Http\Request $request)
    {

       
        $this->validateLogin($request);
        

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }
        
        ////Funcionalidad para Cambio de contraseña al ingreso del sistema
            $passwors_security=PasswordSecurity::where('deleted',0)->get();
            $hoy = date("Y-m-d H:i:00");
            $fecha_actual = strtotime($hoy);
            $fecha_inicio = strtotime($passwors_security[0]->fecha_inicio);

            if($fecha_actual>$fecha_inicio){

                $fecha_cambio_password=User::where('rut',$request->rut)->whereNotIn('id',[1,108])->first();
                
                if($fecha_cambio_password){
                    $token_reset = '';
                            
                    if(is_null($fecha_cambio_password->fecha_cambio_password)){   
                    
                        $token_reset = Str::random(64); 

                        $user_update=User::where('rut', $request->rut)->update([
                            'token_change_password_expire' => $token_reset,
                        ]);      
                        
                        return redirect()->route('resetPassword',['rut'=>$token_reset]);
                    }else{
                        $fecha1= new DateTime($hoy);
                        $fecha2= new DateTime($fecha_cambio_password->fecha_cambio_password);
                        $diff = $fecha1->diff($fecha2);
                        if($diff->days>=$passwors_security[0]->periodo){    
                            $token_reset = Str::random(64); 
                            $user_update=User::where('rut', $request->rut)->update([
                                'token_change_password_expire' => $token_reset
                            ]);                              
                            return redirect()->route('resetPassword',['rut'=>$token_reset]);
                        }                
                    }          
                }
            }
                
        ////
        
        // This section is the only change
        if ($this->guard()->validate($this->credentials($request))) {
            
            $user = $this->guard()->getLastAttempted();

            // Make sure the user is active
            if ($user->active == 1 && $this->attemptLogin($request)) {
                
                // ############### INICIO: PARCHE PARA INTEGRAR SISTEMA DE SAC ###############
                /*//Se crea un nuevo token_sac cuando el usuario inicie sesion
                $user = User::find($user->id);
                $user->token_sac = Str::random(60);
                $user->save();

                //Verificamos que el rol sea solo vendedor y jefe de vendedor
                if ($user->isVendedor() || $user->isJefeVenta()) {
                    //Para la DB de sac el vendedor es rol 1 y el jefe de vendedor es rol 2 por eso asignamos valor a la variable rol
                    if($user->isVendedor()){
                        $role = 1;
                    }

                    if($user->isJefeVenta()){
                        $role = 2;
                    }

                    $usuario = Usuario::where('email', $user->email)->first();
                    //Verificamos que el usuario ya este registrado en la DB de sac sino lo creamos
                    if($usuario){
                        // vendedor
                        if($usuario->role == 1) {
                            $id_jefe_ot = $usuario->jefe_id_ot; //25
                            $jefe = Usuario::where('id_system', $id_jefe_ot)->where('role', 2)->first();
                            if($jefe){
                                $usuario->jefe_id = $jefe->id;
                            }
                        // jefe venta
                        } else if($usuario->role == 2) {
                            $vendedores = Usuario::where('jefe_id_ot', $usuario->id_system)->get()->pluck('id');
                            Usuario::whereIn('id', $vendedores)->update([
                                'jefe_id' =>  $usuario->id
                            ]);
                        }

                        $usuario->token = $user->token_sac;
                        $usuario->save();

                    }else{

                        $jefe_id = null;

                        if($role == 1) {
                            // vendedor
                            // Busco el id del jefe registrado en SAC
                            $jefe = Usuario::where('id_system', $user->jefe_id)->where('system', 'envases-ot')->where('role', 2)->first();
                            if($jefe){
                                $jefe_id = $jefe->id;
                            } else {
                                $jefe_id = null;
                            }
                        }

                        $usuario_nuevo = new Usuario();
                        $usuario_nuevo->name = $user->nombre;
                        $usuario_nuevo->apellido = $user->apellido;
                        $usuario_nuevo->email = $user->email;
                        $usuario_nuevo->role = $role;
                        $usuario_nuevo->jefe_id = $jefe_id;
                        $usuario_nuevo->jefe_id_ot = $user->jefe_id;
                        $usuario_nuevo->token = $user->token_sac;
                        $usuario_nuevo->system = 'envases-ot';
                        $usuario_nuevo->id_system = $user->id;
                        $usuario_nuevo->save();

                        // Si es jefe
                        if($role == 2){
                            $vendedores = Usuario::where('jefe_id_ot', $user->id)->where('role', 1)->where('system', 'envases-ot')->get();
                            Usuario::whereIn('id', $vendedores)->update([
                                'jefe_id' => $usuario_nuevo->id
                            ]);
                        }

                    }
                }*/
                // ############### FIN: PARCHE PARA INTEGRAR SISTEMA DE SAC ###############
                
                // Send the normal successful login response
                return $this->sendLoginResponse($request);
            } else {
                // dd($user);
                // Increment the failed login attempts and redirect back to the
                // login form with an error message.
                $this->incrementLoginAttempts($request);

                //insertar error user no activo:
                throw ValidationException::withMessages([$this->username() => [trans('auth.notactivated')],]);

                return redirect()
                    ->back()
                    ->withInput($request->only($this->username(), 'remember'));
            }
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    // Se logea con RUT
    public function username()
    {
        return 'rut';
    }    

    public function resetPassword(\Illuminate\Http\Request $request)
    {        
        $rut=$request->input('rut');
        $requisitos="Requisitos Contraseña: \n";

        $passwors_security=PasswordSecurity::where('deleted',0)->first();
        if(!is_null($passwors_security->longitud)){
            $requisitos.="- mínimo ".$passwors_security->longitud." dígitos \n";
        }

        if($passwors_security->mayuscula=='S'){
            $requisitos.="- al menos una mayúscula \n";                   
        }

        if($passwors_security->minuscula=='S'){
            $requisitos.="- al menos una minúscula \n";                       
        }

        if($passwors_security->numero=='S'){
            $requisitos.="- al menos un número \n";            
        }
       
        if($passwors_security->caracter=='S'){
            $requisitos.="- un caracter especial ( # ? ! @ $ % + & * - _ )";            
        }

        return view('auth.reset-password',compact('rut','requisitos'));
    }

    public function resetPasswordStore(\Illuminate\Http\Request $request)
    {   
       
        //Se obtienen los valores de seguridad de password
        $passwors_security=PasswordSecurity::where('deleted',0)->first();
        $old_password=User::where('token_change_password_expire', $request->token_reset)->first();
        
        //Validacion de nuevo password y confirmacion iguales
        if($request->password!=$request->password_confirm){
            throw ValidationException::withMessages([$this->username() => ["La nueva contraseña y su confirmación no son iguales"],]);
            return back()->withInput($request->only($this->username(), 'remember'));
        }
       
        //Validacion de nuevo password diferente del anterior
        if(Hash::check($request->password, $old_password->password)){
            throw ValidationException::withMessages([$this->username() => ["La nueva contraseña debe ser diferente a la anterior"],]);
            return back()->withInput($request->only($this->username(), 'remember'));
        }
        $validation_message="Contraseña debe tener:";
        $password_invalido=false;
        //Validacion longitud de Password
        if(!is_null($passwors_security->longitud)){
            $validation_message.=" mínimo ".$passwors_security->longitud." dígitos";
            if(strlen($request->password) < $passwors_security->longitud){
                $password_invalido=true;                   
            }
        }
        //Validacion de Mayusculas
        if($passwors_security->mayuscula=='S'){
            $validation_message.=", al menos una mayúscula";
            if (strtolower($request->password) == $request->password){
                $password_invalido=true;                 
            }            
        }
        //Validacion de Minusculas
        if($passwors_security->minuscula=='S'){
            $validation_message.=", una minúscula";
            if (strtoupper($request->password) == $request->password){
                $password_invalido=true;                 
            }            
        }
        //Validacion de numero
        if($passwors_security->numero=='S'){
            $validation_message.=", un número";
            $numero=false;
            for ($i=0; $i<strlen($request->password); $i++){ 
                $caracter_aux=substr($request->password,$i,1);
                if(is_numeric($caracter_aux)){
                    $numero=true;
                    $i=strlen($request->password);
                }
            }
            if(!$numero){
                $password_invalido=true;   
            }
        }
       
        //Validacion de caracter
        if($passwors_security->caracter=='S'){
            $validation_message.=", un caracter especial (# ? ! @ $ % + & * - _)";
            $caracter=false;
            $array_caracter=['#','?','!','@','$','%','+','&','*','-','_'];
           
            for ($i=0; $i<strlen($request->password); $i++){ 
                $caracter_aux=substr($request->password,$i,1);
                if(in_array($caracter_aux,$array_caracter)){
                    $caracter=true;
                    $i=strlen($request->password);
                }
            }
            if(!$caracter){
                $password_invalido=true;  
            }
        }
        if($password_invalido){
            throw ValidationException::withMessages([$this->username() => [$validation_message]]);
            return back()->withInput($request->only($this->username(), 'remember'));
        }else{
            $user_update=User::where('token_change_password_expire', $request->token_reset)
                        ->update([  'password'              => bcrypt($request->password),
                                    'fecha_cambio_password' => date("Y-m-d"),
                                    'token_change_password_expire' => null]);
            //return redirect()->route('login')->with('success', 'Contraseña actualizada correctamente');
            return redirect()->route('resetPasswordLogin');
        }        
    }
    
    public function resetPasswordLogin(\Illuminate\Http\Request $request)
    {
        $rut=$request->input('rut');
        return view('auth.reset-password-login',compact('rut'));
    }

    public function recoveryPassword(\Illuminate\Http\Request $request)
    {        
        //rut=$request->input('rut');
        return view('auth.recovery-password');
    }

    public function recoveryEmail(\Illuminate\Http\Request $request)
    {        
        $user_email=User::select('email')->where('rut',$request->rut)->first();
       
        if($user_email){
            Mail::to([$user_email->email])->send(new RestablecerContraseña($request->rut));
            return view('auth.recovery-password-email');
        }else{
            throw ValidationException::withMessages([$this->username() => ["El Rut ingresado no es válido"],]);
            return back()->withInput($request->only($this->username(), 'remember'));
        }        
        
    }

    public function resetPasswordRecovery(\Illuminate\Http\Request $request)
    {        
        $token  = $request->input('token');
        $user   = User::where('token_reset_password',$token)->first();
        $requisitos="Requisitos Contraseña: \n";
        $passwors_security=PasswordSecurity::where('deleted',0)->first();
        if(!is_null($passwors_security->longitud)){
            $requisitos.="- mínimo ".$passwors_security->longitud." dígitos \n";
        }

        if($passwors_security->mayuscula=='S'){
            $requisitos.="- al menos una mayúscula \n";
                   
        }

        if($passwors_security->minuscula=='S'){
            $requisitos.="- al menos una minúscula \n";                       
        }

        if($passwors_security->numero=='S'){
            $requisitos.="- al menos un número \n";            
        }
       
        if($passwors_security->caracter=='S'){
            $requisitos.="- un caracter especial ( # ? ! @ $ % + & * - _ )";
            
        }
        
        if($user){
            $now    = Carbon::now();
            if($now > $user->token_reset_password_expire){
                return view('auth.recovery-password-email-expire');
            }else{
                return view('auth.reset-password-recovery',compact('token','requisitos'));
            }
        }else{
            return view('auth.recovery-password-email-expire');
        }
    }

    public function resetPasswordRecoveryStore(\Illuminate\Http\Request $request)
    {   

        //Se obtienen los valores de seguridad de password
        $passwors_security=PasswordSecurity::where('deleted',0)->first();
        $user=User::where('token_reset_password', $request->token_reset)->first();
        
        //Validacion de nuevo password y confirmacion iguales
        if($request->password!=$request->password_confirm){
            throw ValidationException::withMessages([$this->username() => ["La nueva contraseña y su confirmación no son iguales"],]);
            return back()->withInput($request->only($this->username(), 'remember'));
        }
       
        //Validacion de nuevo password diferente del anterior
        if(Hash::check($request->password, $user->password)){
            throw ValidationException::withMessages([$this->username() => ["La nueva contraseña debe ser diferente a la anterior"],]);
            return back()->withInput($request->only($this->username(), 'remember'));
        }

        $validation_message="Requisitos Contraseña:";
        $password_invalido=false;
        //Validacion longitud de Password
        if(!is_null($passwors_security->longitud)){
            $validation_message.=" mínimo ".$passwors_security->longitud." dígitos";
            if(strlen($request->password) < $passwors_security->longitud){
                $password_invalido=true;                   
            }
        }

        //Validacion de Mayusculas
        if($passwors_security->mayuscula=='S'){
            $validation_message.=", al menos una mayúscula";
            if (strtolower($request->password) == $request->password){
                $password_invalido=true;                 
            }            
        }

        //Validacion de Minusculas
        if($passwors_security->minuscula=='S'){
            $validation_message.=", una minúscula";
            if (strtoupper($request->password) == $request->password){
                $password_invalido=true;                 
            }            
        }

        //Validacion de numero
        if($passwors_security->numero=='S'){
            $validation_message.=", un número";
            $numero=false;
            for ($i=0; $i<strlen($request->password); $i++){ 
                $caracter_aux=substr($request->password,$i,1);
                if(is_numeric($caracter_aux)){
                    $numero=true;
                    $i=strlen($request->password);
                }
            }
            if(!$numero){
                $password_invalido=true;   
            }
        }
       
        //Validacion de caracter
        if($passwors_security->caracter=='S'){
            $validation_message.=", un caracter especial (# ? ! @ $ % + & * - _)";
            $caracter=false;
            $array_caracter=['#','?','!','@','$','%','+','&','*','-','_'];
           
            for ($i=0; $i<strlen($request->password); $i++){ 
                $caracter_aux=substr($request->password,$i,1);
                if(in_array($caracter_aux,$array_caracter)){
                    $caracter=true;
                    $i=strlen($request->password);
                }
            }
            if(!$caracter){
                $password_invalido=true;  
            }
        }

        if($password_invalido){
            throw ValidationException::withMessages([$this->username() => [$validation_message],]);
            return back()->withInput($request->only($this->username(), 'remember'));
        }else{
            $user_update=User::where('rut', $user->rut)
                        ->update([  'password'                      => bcrypt($request->password),
                                    'fecha_cambio_password'         => date("Y-m-d"),
                                    'token_reset_password'          => null,
                                    'token_reset_password_expire'   => null]);
            //return redirect()->route('login')->with('success', 'Contraseña actualizada correctamente');
            return redirect()->route('resetPasswordLogin',['rut'=>$request->rut]);
        }        
    }
}


