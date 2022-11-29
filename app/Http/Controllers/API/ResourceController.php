<?php

namespace App\Http\Controllers\API;

use App\Models\Resource;
use App\Helpers\LogActivity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ResourceController extends Controller
{
   
    public function index(){
        $resource = Resource::reorder('id','desc')->paginate(10);
        return response()->json([
            'message'=>'success',
            'record'=>$resource,
        ],200);
    }
    public function edit($slug){
        $resource = Resource::where('slug',$slug)->first();
        if(!$resource){
            return response()->json(['message'=>'Resource not found'],404);
        }else{
            return response()->json([
                'message'=>'success',
                'data'=>$resource,
            ],200);
        }
    }
    public function update(Request $request,$slug){
        $resource = Resource::where('slug',$slug)->first();
        if(!$resource){
            return response()->json(['message'=>'Resource not found'],404);
        }else{
           
            $validator = Validator::make($request->all(), [
       
           
                'name'=> 'required|max:255|min:3',
                'description'=>'required',
               
            ],[
                'name.required'=>'Resource is required',
                'name.max'=>'Resource maksimal 255 karakter',
                'name.min'=>'Resource minimal 3 character',
                'description.required'=>'Description is required',
              
                
            ]);
            if($validator->fails()){
                $message = $validator->errors()->all();
                $msg = [];
                foreach($message as $mess => $arr){
                    $msg[] = [$message[$mess]];
                }
                return response()->json(['message'=>$msg],422);
            }else{
               
                $resource->name = $request->name;
                $resource->description = $request->description;
           
           
                $resource->update();
                LogActivity::addToLog('UPDATE RESOURCE');
                return response()->json(['message'=>'success','data'=>$resource],200);
    
            }
            
        }
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
       
           
            'name'=> 'required|max:255|min:3',
            'description'=>'required',
           
        ],[
            'name.required'=>'Resource is required',
            'name.max'=>'Resource maksimal 255 karakter',
            'name.min'=>'Resource minimal 3 character',
            'description.required'=>'Description is required',
          
            
        ]);
        if($validator->fails()){
            $message = $validator->errors()->all();
            $msg = [];
            foreach($message as $mess => $arr){
                $msg[] = [$message[$mess]];
            }
            return response()->json(['message'=>$msg],422);
        }else{
            $resource = new Resource();
            $resource->name = $request->name;
            $resource->description = $request->description;
       
            $resource->created_by = $request->user()->id;
            $resource->save();
            LogActivity::addToLog('STORE RESOURCE');
            return response()->json(['message'=>'success'],200);

        }
    }
    public function destroy(Request $request, $slug){
        $resource = Resource::where('slug',$slug)->first();
        if(!$resource){
            return response()->json(['message'=>'Resource not found'],404);
        }else{
            $resource->delete();
            LogActivity::addToLog('DELETE RESOURCE');
            return response()->json([
                'message'=>'Resource successfully deleted',
                
            ],200);
        }
    }
}
