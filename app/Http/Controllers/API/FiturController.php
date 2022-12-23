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
            'statusCode'=>200,
            'status'=>'OK'
        ]);
    }
    public function detail($slug){
        $fitur = Fitur::where('slug',$slug)->first();
        if(!$fitur){
            return response()->json(['message'=>'Feature not found','status'=>'NOTFOUND','statusCode'=>404]);
        }else{
            $resource = [];
            
            $res = Fitur_resource::where('fitur_id',$fitur->id)->get();
            if($res->count() > 0){
                foreach($res as $re){
                    $resource[] = ['resource'=>$re->sumber->name,'id'=>$re->id,'value'=>$re->value,'capacity'=>$re->capacity];
                }
            }
            $price = Fitur_price::where('fitur_id',$fitur->id)->orderBy('price','asc')->get();
            return response()->json(['message'=>'success','feature'=>$fitur,'resource'=>$resource,'price'=>$price,'status'=>"OK",'statusCode'=>200]);
        }
    }
    public function edit($slug){
        $fitur = Fitur::where('slug',$slug)->first();
        if(!$fitur){
            return response()->json(['message'=>'Feature not found','status'=>'NOTFOUND','statusCode'=>404]);
        }else{
            
            return response()->json(['message'=>'success','feature'=>$fitur,'status'=>'OK','statusCode'=>200 ]);
        }
    }
    public function checkResource($slug,$id){
        $fitur = Fitur::where('slug',$slug)->first();
        if(!$fitur){
            return response()->json(['message'=>'Feature not found','status'=>'NOTFOUND','statusCode'=>404]);

        }else{
            $cek = Fitur_resource::where(['fitur_id'=>$fitur->id,'resource_id'=>$id])->first();
            if($cek !== null){
                return response()->json(['condition'=>true,'value'=>$cek->value,'capacity'=>$cek->capacity,'statusCode'=>200]);

            }else{
                return response()->json(['condition'=>false,'statusCode'=>200]);

            }
        }
    }
    public function editPrice($slug,$price){
        $fitur = Fitur::where('slug',$slug)->first();
        if(!$fitur){
            return response()->json(['message'=>'Feature not found','status'=>'NOTFOUND','statusCode'=>404]);
        }else{
            $price = Fitur_price::where(['fitur_id'=>$fitur->id,'slug'=>$price])->first();
            if($price !== null){
               
                return response()->json(['message'=>'success','feature'=>$fitur,'price'=>$price,'status'=>'success','statusCode'=>200]);

            }else{
                return response()->json(['message'=>'Price not found','status'=>'notfound','statusCode'=>404]);


            }
        }
        
    }
    public function updatePrice(Request $request,$slug,$price){
        $fitur = Fitur::where('slug',$slug)->first();
        if(!$fitur){
            return response()->json(['message'=>'Feature not found','status'=>'not_found','statusCode'=>404]);
        }else{
            $price = Fitur_price::where(['fitur_id'=>$fitur->id,'slug'=>$price])->first();
            if($price){
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
                    $message = $validator->errors();
                  
                    return response()->json(['message'=>$message,'statusCode'=>422,'status'=>'validation errors']);
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
                    return response()->json(['message'=>'Price successfully updated','feature'=>$fitur,'price'=>$price,'status'=>'success','statusCode'=>200]);
                }

              

            }else{
                return response()->json(['message'=>'Price not found','status'=>'not found','statusCode'=>404]);

            }
        }
        
    }

    public function best($slug){
        $fitur = Fitur::where('slug',$slug)->first();
        if(!$fitur){
            return response()->json(['message'=>'Feature not found','statusCode'=>404,'status'=>'not found']);
        }else{
            Fitur::where('best','y')->update(['best'=>'n']);
            $fitur->best = 'y';
            $fitur->update();
            return response()->json(['message'=>'Successfully update best feature','statusCode'=>200,'status'=>'success']);
        }
    }
    public function destroy($slug){
        $fitur = Fitur::where('slug',$slug)->first();
        if(!$fitur){
            return response()->json(['message'=>'Feature not found','status'=>'not found','statusCode'=>404]);
        }else{
            Fitur_resource::where('fitur_id',$fitur->id)->delete();
            Fitur_price::where('fitur_id',$fitur->id)->delete();

            $fitur->delete();
            LogActivity::addToLog('DELETE FEATURE');
            return response()->json(['message'=>'Feature successfully deleted','status'=>'success','statusCode'=>200]);
        }
    }
    public function destroyResource($slug,$id){
        $fitur = Fitur::where('slug',$slug)->first();
        if(!$fitur){
            return response()->json(['message'=>'Feature not found','status'=>'not found','statusCode'=>404]);
        }else{
            Fitur_resource::where('id',$id)->delete();
            LogActivity::addToLog('DELETE RESOURCE');

           
            return response()->json(['message'=>'Feature resource successfully deleted','status'=>'success','statusCode'=>200]);

        }
    }
    public function destroyPrice($slug,$id){
        $fitur = Fitur::where('slug',$slug)->first();
        if(!$fitur){
            return response()->json(['message'=>'Feature not found','status'=>'not found','statusCode'=>404]);
        }else{
            Fitur_price::where('id',$id)->delete();
            LogActivity::addToLog('DELETE PRICE');

           
            return response()->json(['message'=>'Feature price successfully deleted','status'=>'success','statusCode'=>200]);

        }
    }
    public function update(Request $request,$slug){
        $fitur = Fitur::where('slug',$slug)->first();
        if(!$fitur){
            return response()->json(['message'=>'Feature not found','status'=>'not found','statusCode'=>404]);

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
                $message = $validator->errors();
              
                return response()->json(['message'=>$message,'statusCode'=>422,'status'=>'validations error']);
            }else{
                LogActivity::addToLog('UPDATE FEATURE');
                $fitur->name = $request->name;
                $fitur->description = $request->description;
              
                $fitur->update();
    
                return response()->json(['message'=>'Feature price successfully updated','status'=>'success','statusCode'=>200]);
            }
        }
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
       
           
            'name'=> 'required|max:255|min:3',
            'description'=>'required',
           
        ],[
            'name.required'=>'Feature is required',
            'name.max'=>'Feature maksimal 255 karakter',
            'name.min'=>'Feature minimal 3 character',
            'description.required'=>'Description is required',
          
            
        ]);
        if($validator->fails()){
            $message = $validator->errors();
           
            return response()->json(['message'=>$message,'statusCode'=>422,'status'=>'validations error']);

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
                'status'=>'success',
                'slug'=>$fitur->slug,
                'statusCode'=>200
            ]);
        }
    }
    public function storePrice(Request $request,$slug){
        $fitur = Fitur::where('slug',$slug)->first();
        if(!$fitur){
            return response()->json(['message'=>'Feature not found','status'=>'success','statusCode'=>404]);
        }else{
            $validator = Validator::make($request->all(), [
                'name'=> 'required',
                'name.*'=> 'required|max:255|min:3',

                'price'=>'required',
                'price.*'=>'required',

                'discount'=>'required',
                'discount.*'=>'required|numeric',

                'duration'=>'required',
                'duration.*'=>'required|in:monthly,yearly,daily,weekly',  
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
                'duration.*.in'=>'Duration type not valid',
            
            ]);
            if($validator->fails()){
                $message = $validator->errors();
                return response()->json(['message'=>$message,'statusCode'=>422,'status'=>'validations error']);

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
                    return response()->json([
                        'message'=>'Successfull inserted price',
                        'status'=>'success',
                        'slug'=>$fitur->slug,
                        'statusCode'=>200
                    ]);
                }else{
                    
                    return response()->json(['message'=>'Price not found','status'=>'success','statusCode'=>404]);

                }
            }
        }
    }
    public function storeResource(Request $request,$slug){
        $fitur = Fitur::where('slug',$slug)->first();
        if(!$fitur){
            return response()->json(['message'=>'Feature not found','status'=>'success','statusCode'=>404]);
        }else{
            $validator = Validator::make($request->all(), [
               
                
                'value'=>'required',
                'value.*'=>'required|in:y,n',
 
            ],[
               
                'value.required'=>'Resource value is required',
                'value.*.required'=>'Resource value is required',
                'value.*.in'=>'Resource value must be yes or no',

     
            ]);
            if($validator->fails()){
                $message = $validator->errors();
                return response()->json(['message'=>$message,'statusCode'=>422,'status'=>'validations error']);

            }else{
               
                $value = $request->value;
                $capacity = $request->capacity;
                $resource = Resource::reorder('id','desc')->get();

                if($resource->count() > 0){
                    Fitur_resource::where('fitur_id',$fitur->id)->delete();
                    $no = 0;
                    foreach($resource as $res){
                      
                         
                                $reso = new Fitur_resource();
                                $reso->fitur_id = $fitur->id;
                                $reso->resource_id = $res->id;
                                $reso->value = $value[$no];
                                if(empty($capacity[$no])){
                                    $reso->capacity = null;
                                }else{
                                    $reso->capacity= $capacity[$no];
                                }
                                $reso->created_by = $request->user()->id;
                                $reso->save();
                        $no++;
                          
                        
                    }
                    LogActivity::addToLog('STORE RESOURCE');
                    return response()->json([
                        'message'=>'Successfull inserted resource',
                        'status'=>'success',
                        'statusCode'=>200
                    ]);
                }else{
                    
                    return response()->json(['message'=>'Price not inserted','status'=>'success','statusCode'=>404]);

                }
            }
        }
    }
    
}
