<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Helpers\LogActivity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
       
           
            'email'=> 'required|email',
            'password'=>'required',
                    
           
        ],[
            'email.required'=>'Email is required',
            'email.email'=>'Email not valid',
            'password.required'=>'Passord is required',
          
          
            
        ]);
        if($validator->fails()){
            $statusCode = 422;
            $status = 'need validations';
            $msg = $validator->errors();
            return response()->json(['status'=>$status,'statusCode'=>$statusCode,'msg'=>$msg]);

        }else{
            $user = User::where('email',$request->email)->first();
            if(!$user OR !Hash::check($request->password,$user->password)){
                $statusCode = 201;
                $msg = ['Wrong email password'];
                $status = 'unauthorize';
                return response()->json(['status'=>$status,'statusCode'=>$statusCode,'msg'=>$msg]);
            }else{
              
                LogActivity::addToLog('LOGIN');
                
                $token = $user->createToken('token-name')->plainTextToken;
               
                $userArr = ['email'=>$user->email,'name'=>$user->name];
                return response()->json(['status'=>'authorize','statusCode'=>200,'user'=>$userArr,'token'=>$token]);

            }
        }
       
    }
    public function logout(Request $request){
        $user = $request->user();
        LogActivity::addToLog('LOGOUT');
        $request->user()->currentAccessToken()->delete();
        
        return response()->json(['message'=>'Successfull logout'],200);
    }
}
