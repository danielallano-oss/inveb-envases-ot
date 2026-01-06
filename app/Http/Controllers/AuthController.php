<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // public function signup(Request $request)
    // {
    //     $request->validate([
    //         'name'     => 'required|string',
    //         'email'    => 'required|string|email|unique:users',
    //         'password' => 'required|string|confirmed',
    //     ]);
    //     $user = new User([
    //         'name'     => $request->name,
    //         'email'    => $request->email,
    //         'password' => bcrypt($request->password),
    //     ]);
    //     $user->save();
    //     return response()->json([
    //         'message' => 'Successfully created user!'
    //     ], 201);
    // }
    public function login(Request $request)
    {
        $request->validate([
            'email'       => 'required_without:rut|string|email',
            'rut'       => 'required_without:email',
            'password'    => 'required|string',
            'remember_me' => 'boolean',
        ]);
        if (is_null(request('email')) or trim(request('email')) == '')
            $credentials = request(['rut', 'password']);
        else
            $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
        $user = $request->user();
        if (!$user->isVendedor() && !$user->isAPI()) {
            return response()->json([
                'message' => 'Rol no permitido'
            ], 403);
        }
        
        if($user->role->work_space_id > 0){
            $area = $user->role->area->id;
        }else{
            $area = NULL;
        }

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => Carbon::parse(
                $tokenResult->token->expires_at
            )
                ->toDateTimeString(),
            'user'          => $user,
            'area'          => $area,
        ]);
    }
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' =>
        'Successfully logged out']);
    }
    // public function user(Request $request)
    // {
    //     return response()->json($request->user());
    // }
}
