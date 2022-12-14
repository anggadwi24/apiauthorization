<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Helpers\LogActivity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    public function index(Request $request){
        $per_page = $request->get('per_page');
        $user = User::reorder('id','desc')->paginate($per_page);
        $collection = UserResource::collection($user);
        LogActivity::addToLog('LIST USERS');
        return response()->json([
            'message'=>'success',
            'record'=>$collection,
            'status'=>'success',
            'statusCode'=>200,
        ]);
    }

    public function detail($email){
        $user = User::where('email',$email)->first();
        if($user){
            $collection = new UserResource($user);
            return response()->json(['message'=>'success','user'=>$collection,'status'=>'ok','statusCode'=>200]);
        }else{
            return response()->json(['message'=>'User not found','status'=>'error','statusCode'=>404]);
        }
    }

    public function edit($email){
        $user = User::where('email',$email)->first();
        if($user){
            $collection = new UserResource($user);
            return response()->json(['message'=>'success','user'=>$collection,'status'=>'ok','statusCode'=>200]);
        }else{
            return response()->json(['message'=>'User not found','status'=>'error','statusCode'=>404]);
        }
    }
    public function update(Request $request,$email){
        $user = User::where('email',$email)->first();
        if($user){  
            $validator = Validator::make($request->all(), [
       
           
                'name'=> 'required|max:255|min:3',
                'email'=> 'required|max:255|min:10|unique:users,email,'.$user->id.',id|email',
              
                'level'=>'required|in:admin,user',
               
            ],[
                'name.required'=>'Resource is required',
                'name.max'=>'Resource maximal 255 character',
                'name.min'=>'Resource minimal 3 character',
                'email.required'=>'Email is required',
                'email.email'=>'Email not valid',
                'email.unique'=>'Email has been used',
                'level.required'=>'Level is required',
                'level.in'=>'Level not match',
              
              
                
            ]);
            if($validator->fails()){
                $message = $validator->errors();
              
                return response()->json(['message'=>$message,'status'=>'error','statusCode'=>422]);
            }else{
                $password = $request->password;
                $user->name = $request->name;
                $user->email = $request->email;
                if(!empty($password)){
                    $user->password = bcrypt($request->password);

                }
                $user->level = $request->level;
              
                $user->update();
                return response()->json(['message'=>'Success updated users','user'=>new UserResource($user),'status'=>'ok','statusCode'=>200]);
            }
        }else{
            return response()->json(['message'=>'User not found','status'=>'error','statusCode'=>404]);

        }
    }
    
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
       
           
            'name'=> 'required|max:255|min:3',
            'email'=>'required|unique:users,email|email',
            'password'=>'required|min:6',
            'level'=>'required|in:admin,user',
           
        ],[
            'name.required'=>'Name is required',
            'name.max'=>'Name maximal 255 character',
            'name.min'=>'Name minimal 3 character',
            'email.required'=>'Email is required',
            'email.email'=>'Email not valid',
            'email.unique'=>'Email has been used',
            'level.required'=>'Level is required',
            'level.in'=>'Level not match',
            'password.required'=>'Password is required',
            'password.min'=>'Password min 6 character',
          
            
        ]);
        if($validator->fails()){
            $message = $validator->errors();
          
            return response()->json(['message'=>$message,'status'=>'error','statusCode'=>422]);
        }else{
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->level = $request->level;
            $user->company_id = null;
            $user->save();
            return response()->json(['message'=>'Success created users','user'=>new UserResource($user),'status'=>'ok','statusCode'=>200]);
        }
    }
    public function destroy($email){
        $user = User::where('email',$email)->first();
        if($user){
            $user->delete();
            return response()->json(['message'=>'success','status'=>'ok','statusCode'=>200]);
        }else{
            return response()->json(['message'=>'User not found','status'=>'error','statusCode'=>404]);
        }
    }
}
