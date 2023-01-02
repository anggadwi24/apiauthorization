<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Image;

class ProfileController extends Controller
{
    public function index(Request $request){
        $user = $request->user();
        $collection = new UserResource($user);
        return response()->json(['message'=>'success','data'=>$collection,'statusCode'=>200,'status'=>'success']);
      }
     public function edit(Request $request){
        $id =  $request->user()->id;
        $validator = Validator::make($request->all(), [
       
           
            'name'=> 'required|max:255|min:3',
            'email'=> 'required|max:255|min:10|unique:users,email,'.$id.',id|email',
          
          
            'phone'=>'required|max:25',
            'nickname'=>'required|max:255|min:3',
           
        ],[
            'name.required'=>'Name is required',
            'name.max'=>'Name maximal 255 character',
            'name.min'=>'Name minimal 3 character',
            'email.required'=>'Email is required',
            'email.email'=>'Email not valid',
            'email.unique'=>'Email has been used',
        
          
            'phone.required'=>'Phone is required',
            'phone.max'=>'Phone maximal 25 characters',
            'nickname.required'=>'Nickname is required',
            'nickname.min'=>'Nickname min 3 characters',
            'nickname.max'=>'Nickname max 255 characters',
          
          
            
        ]);
        if($validator->fails()){
            $message = $validator->errors();
          
            return response()->json(['message'=>$message,'status'=>'error','statusCode'=>422]);
        }else{
            $password = $request->password;
            $user = User::where('id',$id)->first();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->nickname = $request->nickname;
            $user->phone = $request->phone;
            $user->update();
            return response()->json(['message'=>'Profile successfully updated','status'=>'ok','statusCode'=>200]);
        }
     } 
     public function image(Request $request){
        $user = User::where('id',$request->user()->id)->first();
        if($request->hasfile('image')){
            $validator = Validator::make($request->all(), [

                'image' => 'required|mimes:jpeg,png,jpg|max:5048',
            
            ],[
                
                'image.required'=>'Image is required',
                'image.mimes'=>'Image can format jpeg,png,jpg',
                'image.max'=>'Image maximal 5mb',
            
            ]);
            if($validator->fails()){
                $message = $validator->errors();
                return response()->json(['message'=>$message,'status'=>'validations','statusCode'=>422]);
            }else{
                $file = $request->file('image');
                $filenames = Str::slug($user->name). '.' . $file->getClientOriginalExtension();

                $image =  Image::make($file->getRealPath());
                $image->resize(500, null, function ($constraint) {
                $constraint->aspectRatio();
                });  
                $image->save( public_path('/uploads/user/' . $filenames , 20) );

                $user->photo = $filenames;
                $user->save();
                return response()->json(['message'=>'Success change image','status'=>'success','statusCode'=>200,'image'=>$filenames]);

            }
        }else{
            return response()->json(['message'=>'Select image','status'=>'error','statusCode'=>401]);

        }
     }
     public function password(Request $request){
        $id =  $request->user()->id;
        $user = User::where('id',$id)->first();
        $validator = Validator::make($request->all(), [
          
           
            'password'=>['required','max:100','min:6','confirmed',Password::min(6)
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised(),],
           
        ],[
           
           
            'password.max'=>'New password maximal 100 characters',
            'password.required'=>'New password is required',
            'password.min'=>'New password min 6 characters',
            'password.confirmed'=>'New Password and Confirm Password not same',
          
          
            
        ]);
        if($validator->fails()){
            $message = $validator->errors();
          
            return response()->json(['message'=>$message,'status'=>'error','statusCode'=>422]);
        }else{
            $old = bcrypt($request->old);
           
         
            $password = bcrypt($request->password);
            $user->password = $password;
            $user->update();
            return response()->json(['message'=>'Password successfully changed','status'=>'success','statusCode'=>200]);

         
        }
     }
}
