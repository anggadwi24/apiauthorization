<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\FeatureResource;
use App\Http\Resources\ProvinceResource;
use App\Models\City;
use App\Models\Fitur;
use App\Models\Province;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function getProvince(){
        $data = Province::reorder('name','asc')->get();
        $collection = ProvinceResource::collection($data);
       

        return response()->json(['message'=>'success','data'=>$collection,'status'=>'ok','statusCode'=>200]);
    }
    public function getFeature(){
        $data = Fitur::reorder('name','asc')->get();
        $collection = FeatureResource::collection($data);
        
        return response()->json(['message'=>'success','data'=>$collection,'status'=>'ok','statusCode'=>200]);


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
