<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Helpers\LogActivity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;

class AuthController extends Controller
{
    public function login(Request $request){
        $user = User::where('email',$request->email)->first();
        if(!$user OR !Hash::check($request->password,$user->password)){
            return response()->json(['message'=>'Unauthorized'],401);
        }else{
            // $token = $request->user()->createToken($request->token_name)->plainTextToken;
            LogActivity::addToLog('LOGIN');
            $token = $user->createToken('token-name')->plainTextToken;
            return response()->json([
                'message'=>'success',
                'user'=>$user,
                'token'=>$token
            ],200);
        }
    }
    public function logout(Request $request){
        $user = $request->user();
        LogActivity::addToLog('LOGOUT');
        $request->user()->currentAccessToken()->delete();
        
        return response()->json(['message'=>'Successfull logout'],200);
    }
}
