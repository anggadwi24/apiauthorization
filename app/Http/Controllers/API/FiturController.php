<?php

namespace App\Http\Controllers\API;

use App\Models\Fitur;
use App\Models\Resource;
use App\Models\Fitur_price;
use App\Helpers\LogActivity;
use Illuminate\Http\Request;
use App\Models\Fitur_resource;
use App\Http\Controllers\Controller;
use App\Http\Resources\FiturResource;
use Illuminate\Support\Facades\Validator;

class FiturController extends Controller
{
    public function index(){
        $response = [];
        $fitur = Fitur::reorder('id','desc')->get();
        $collection = FiturResource::collection($fitur);
        // foreach($fitur as $row){
        //     $resource = [];
            
        //     $res = Fitur_resource::where('fitur_id',$row->id)->get();
        //     if($res->count() > 0){
        //         foreach($res as $re){
        //             $resource[] = ['resource'=>$re->sumber->name,'id'=>$re->id,'value'=>$re->resource];
        //         }
        //     }
        //     $price = Fitur_price::where('fitur_id',$row->id)->orderBy('price','asc')->first();
        //     if($price){
        //         $response[] = ['feature'=>$row,'resource'=>$resource,'price'=>$price];
        //     }

           
        // }
        return response()->json([
            'data'=>$collection,
            'message'=>null,
        ],200);
    }
    public function detail($slug){
        $fitur = Fitur::where('slug',$slug)->first();
        if(!$fitur){
            return response()->json(['message'=>'Feature not found'],404);
        }else{
            $resource = [];
            
            $res = Fitur_resource::where('fitur_id',$fitur->id)->get();
            if($res->count() > 0){
                foreach($res as $re){
                    $resource[] = ['resource'=>$re->sumber->name,'id'=>$re->id,'value'=>$re->resource,'capacity'=>$re->capacity];
                }
            }
            $price = Fitur_price::where('fitur_id',$fitur->id)->orderBy('price','asc')->get();
            return response()->json(['message'=>'success','feature'=>$fitur,'resource'=>$resource,'price'=>$price],200);
        }
    }
    public function edit($slug){
        $fitur = Fitur::where('slug',$slug)->first();
        if(!$fitur){
            return response()->json(['message'=>'Feature not found'],404);
        }else{
            
            return response()->json(['message'=>'success','feature'=>$fitur],200);
        }
    }
    public function checkResource($slug,$id){
        $fitur = Fitur::where('slug',$slug)->first();
        if(!$fitur){
            return response()->json(['message'=>'Feature not found'],404);
        }else{
            $cek = Fitur_resource::where(['fitur_id'=>$fitur->id,'resource_id'=>$id])->first();
            if($cek !== null){
                return response()->json(['condition'=>true,'value'=>$cek->value,'capacity'=>$cek->capacity],200);

            }else{
                return response()->json(['condition'=>false],200);

            }
        }
    }
    public function editPrice($slug,$price){
        $fitur = Fitur::where('slug',$slug)->first();
        if(!$fitur){
            return response()->json(['message'=>'Feature not found'],404);
        }else{
            $price = Fitur_price::where(['fitur_id'=>$fitur->id,'slug'=>$price])->first();
            if($price !== null){
               
                return response()->json(['message'=>'success','feature'=>$fitur,'price'=>$price],200);

            }else{
                return response()->json(['message'=>'Price not found'],404);


            }
        }
        
    }
    public function updatePrice(Request $request,$slug,$price){
        $fitur = Fitur::where('slug',$slug)->first();
        if(!$fitur){
            return response()->json(['message'=>'Feature not found'],404);
        }else{
            $price = Fitur_price::where(['fitur_id'=>$fitur->id,'slug'=>$price])->first();
            if($price === null){
                $validator = Validator::make($request->all(), [
                    'name'=> 'required|max:255|min:3',
                  
    
                    'price'=>'required',
                  
    
                    'discount'=>'required|numeric',
                    
    
                   
                  
                ],[
                    'name.required'=>'Name price is required',
                
                    'name.max'=>'Name price maksimal 255 karakter',
                    'name.min'=>'Name price minimal 3 character',
    
                    'price.required'=>'Price is required',
                 
    
                    'discount.required'=>'Discount percentage is required',
                 
                    'discount.numeric'=>'Discount percentage is numeric',
    
              
                
                ]);
                if($validator->fails()){
                    $message = $validator->errors()->all();
                    $msg = [];
                    foreach($message as $mess => $arr){
                        $msg[] = [$message[$mess]];
                    }
                    return response()->json(['message'=>$msg],422);
                }else{
                    $name = $request->name;
                    $price = $request->price;
                    $discount = $request->discount;
                    $newPrice = str_replace(array('.',','),'',$price);
                  
                    $price->name = $name;
                    $price->price = $newPrice;
                    $price->discount_percent = $discount;
                    $price->discount = ($newPrice*$discount)/100;

                 
                    $price->update();
                    LogActivity::addToLog('UPDATE PRICE');
                    return response()->json(['message'=>'success','feature'=>$fitur,'price'=>$price],200);
                }

              

            }else{
                return response()->json(['message'=>'success','feature'=>$fitur,'price'=>$price],200);

            }
        }
        
    }

    public function best($slug){
        $fitur = Fitur::where('slug',$slug)->first();
        if(!$fitur){
            return response()->json(['message'=>'Feature not found'],404);
        }else{
            Fitur::where('best','y')->update(['best'=>'n']);
            $fitur->best = 'y';
            $fitur->update();
            return response()->json(['message'=>'success'],200);
        }
    }
    public function destroy($slug){
        $fitur = Fitur::where('slug',$slug)->first();
        if(!$fitur){
            return response()->json(['message'=>'Feature not found'],404);
        }else{
            Fitur_resource::where('fitur_id',$fitur->id)->delete();
            Fitur_price::where('fitur_id',$fitur->id)->delete();

            $fitur->delete();
            LogActivity::addToLog('DELETE FEATURE');
            return response()->json(['message'=>'success'],200);
        }
    }
    public function destroyResource($slug,$id){
        $fitur = Fitur::where('slug',$slug)->first();
        if(!$fitur){
            return response()->json(['message'=>'Feature not found'],404);
        }else{
            Fitur_resource::where('id',$id)->delete();
            LogActivity::addToLog('DELETE RESOURCE');

           
            return response()->json(['message'=>'success'],200);
        }
    }
    public function destroyPrice($slug,$id){
        $fitur = Fitur::where('slug',$slug)->first();
        if(!$fitur){
            return response()->json(['message'=>'Feature not found'],404);
        }else{
            Fitur_price::where('id',$id)->delete();
            LogActivity::addToLog('DELETE PRICE');

           
            return response()->json(['message'=>'success'],200);
        }
    }
    public function update(Request $request,$slug){
        $fitur = Fitur::where('slug',$slug)->first();
        if(!$fitur){
            return response()->json(['message'=>'Feature not found'],404);
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
                LogActivity::addToLog('UPDATE FEATURE');
                $fitur->name = $request->name;
                $fitur->description = $request->description;
              
                $fitur->update();
    
                return response()->json([
                    'message'=>'Successfull updated feature',
                    'feature'=>$fitur,
                ],200);
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
            $fitur = new Fitur();
            $fitur->name = $request->name;
            $fitur->description = $request->description;
            $fitur->best = 'n';
            $fitur->created_by = $request->user()->id;
            $fitur->save();
            LogActivity::addToLog('STORE FEATURE');
            return response()->json([
                'message'=>'Successfull created feature',
                'feature'=>$fitur,
            ],200);
        }
    }
    public function storePrice(Request $request,$slug){
        $fitur = Fitur::where('slug',$slug)->first();
        if(!$fitur){
            return response()->json(['message'=>'Feature not found'],404);
        }else{
            $validator = Validator::make($request->all(), [
                'name'=> 'required',
                'name.*'=> 'required|max:255|min:3',

                'price'=>'required',
                'price.*'=>'required',

                'discount'=>'required',
                'discount.*'=>'required|numeric',

                'duration'=>'required',
                'duration.*'=>'required|in:month,year',  
            ],[
                'name.required'=>'Name price is required',
                'name.*.required'=>'Name price is required',
                'name.*.max'=>'Name price maksimal 255 karakter',
                'name.*.min'=>'Name price minimal 3 character',

                'price.required'=>'Price is required',
                'price.*.required'=>'Price is required',

                'discount.required'=>'Discount percentage is required',
                'discount.*.required'=>'Discount percentage is required',
                'discount.*.numeric'=>'Discount percentage is numeric',

                'duration.required'=>'Duration is required',
                'duration.*.required'=>'Duration is required',
                'duration.*.in'=>'Duration can only monthly or yearly',
            
            ]);
            if($validator->fails()){
                $message = $validator->errors()->all();
                $msg = [];
                foreach($message as $mess => $arr){
                    $msg[] = [$message[$mess]];
                }
                return response()->json(['message'=>$msg],422);
            }else{
                $name = $request->name;
                $price = $request->price;
                $discount = $request->discount;
                $duration = $request->duration;

                if(count($name) > 0){
                    foreach($name as $pr => $val){
                        if(!empty($name[$pr]) OR !empty($price[$pr]) OR !empty($discount[$pr]) OR !empty($duration[$pr])){
                            $newPrice = str_replace(array('.',','),'',$price[$pr]);
                            $fp = new Fitur_price();
                            $fp->name = $name[$pr];
                            $fp->price = $newPrice;
                            $fp->discount_percent = $discount[$pr];
                            $fp->discount = ($newPrice*$discount[$pr])/100;
                            $fp->fitur_id = $fitur->id;
                            $fp->created_by = $request->user()->id;
                            $fp->save();

                        }
                    }
                    LogActivity::addToLog('STORE PRICE');
                    return response()->json(['message'=>'Successfull created price of feature','feature'=>$fitur],200);
                }else{
                    return response()->json(['message'=>'Price not inserted'],404);
                }
            }
        }
    }
    public function storeResource(Request $request,$slug){
        $fitur = Fitur::where('slug',$slug)->first();
        if(!$fitur){
            return response()->json(['message'=>'Feature not found'],404);
        }else{
            $validator = Validator::make($request->all(), [
                'resource'=> 'required',
                'resource.*'=> 'required|distinct',
                
                'value'=>'required',
                'value.*'=>'required|in:y,n',
 
            ],[
                'resource.required'=>'Resource is required',
                'resource.*.required'=>'Resource is required',
                'resource.*.distinct'=>'Resource can`nt be same',

                'value.required'=>'Resource value is required',
                'value.*.required'=>'Resource value is required',
                'value.*.in'=>'Resource value must be yes or no',

     
            ]);
            if($validator->fails()){
                $message = $validator->errors()->all();
                $msg = [];
                foreach($message as $mess => $arr){
                    $msg[] = [$message[$mess]];
                }
                return response()->json(['message'=>$msg],422);
            }else{
                $resource = $request->resource;
                $value = $request->value;
                $capacity = $request->capacity;
           

                if(count($resource) > 0){
                    foreach($resource as $res => $val){
                        if(!empty($resource[$res]) AND !empty($resource[$res]) ){
                          $find = Resource::where('slug',$resource[$res])->first();
                          if($find){
                                $reso = new Fitur_resource();
                                $reso->fitur_id = $fitur->id;
                                $reso->resource_id = $find->id;
                                $reso->value = $value[$res];
                                if(empty($capacity[$res])){
                                    $reso->capacity = null;
                                }else{
                                    $reso->capcity= $capacity[$res];
                                }
                                $reso->created_by = $request->user()->id;
                                $reso->save();
                          }
                        }
                    }
                    LogActivity::addToLog('STORE RESOURCE');
                    return response()->json(['message'=>'Successfull created resource of feature','feature'=>$fitur],200);
                }else{
                    return response()->json(['message'=>'Resource not inserted'],404);
                }
            }
        }
    }
    
}
