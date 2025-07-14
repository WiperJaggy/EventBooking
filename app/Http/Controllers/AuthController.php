<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){
        $user = User::create([
            "name"=> $request->name,
            "email"=> $request->email,
            "password"=> Hash::make($request->password),
        ]);
        $token= $user->createToken("auth_token")->plainTextToken;
    return response()->json([
        'message' => 'User registerd successfully. ',
        'user'=>[
            'id'=>$user->id,
            'name'=>$user->name,
            'email'=>$user->email
        ],
        'access_token'=>$token,
        'token_type'=>'Bearer'
    ],201);   
    }

//login

public function login(LoginRequest $request){

if(!Auth::attempt($request->only('email','password'))){
    return response()->json([
        'message'=>'Invalid login credentials'
    ],401);
}

$user=Auth::user();

$token=$user->createToken('auth_token')->plainTextToken;

 return response()->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200); // 200 OK



//     $user = User::where('email', $request->email)->first();

//     if(!$user || ! Hash::check($request->password, $user->password)){
//         return response()->json(['message'=>'invalid credentials'],401);
// }


//         $token = $user->createToken('api-token')->plainTextToken;

//         return response()->json([
//             'message' => 'Login successful.',
//             'token'   => $token,
//         ]);
}

   public function logout(Request $request){
            // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
   }

public function user(Request $request)
{
    if (!$request->user()) {
        return response()->json([
            'message' => 'Please log in to access this resource.',
        ], 401); // 401 = Unauthorized
    }

    return response()->json([
        'user' => [
            'id' => $request->user()->id,
            'name' => $request->user()->name,
            'email' => $request->user()->email,
        ],
    ]);
}
}