<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(){
        $category = Category::select('name')->orderBy('name','asc')->get();
        $name = [];
        foreach($category as $ca) {
            $name[] = $ca->name;
        }   
        return response()->json(['record'=>$name,'statusCode'=>200,'status'=>'OK']);
    }

    public function search(Request $request){
        $keyword = $request->get('keyword');
        if(!empty($keyword)){
            $cat = Category::select('name')->where('name','LIKE','%'.$keyword.'%')->get();
            return response()->json(['record'=>$cat],200);
        }else{
            return response()->json(['message'=>'Bad Request'],400);
        }
    }

   
}
