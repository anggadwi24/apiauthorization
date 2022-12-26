<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Province;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function getProvince(){
        $data = Province::reorder('name','asc')->get();
        return response()->json(['message'=>'success','data'=>$data,'status'=>'ok','statusCode'=>200]);
    }

    public function getCity($province){
        $data = City::where('province_id',$province)->reorder('name','asc');
        if($data->count() <= 0){
            return response()->json(['message'=>'Province not have city','status'=>'notfound','statusCode'=>404]);
        }else{
            return response()->json(['message'=>'success','status'=>'ok','statusCode'=>200,'data'=>$data->get()]);
            
        }
    }
}
