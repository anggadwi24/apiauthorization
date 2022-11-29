<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\City;
use App\Models\Fitur;
use App\Models\Company;
use App\Models\Category;
use App\Models\Fitur_price;
use Illuminate\Support\Str;
use App\Helpers\LogActivity;
use Illuminate\Http\Request;
use App\Models\Company_payment;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use App\Http\Resources\CompanyResource;
use App\Models\Company_referal;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    public function index(Request $request){
        $per_page = $request->get('per_page');
        if(empty($per_page)){
            $per_page = 10;
        }
        $company = Company::orderBy('id','desc')->paginate($per_page);
        $collection = CompanyResource::collection($company);
        return response()->json(['message'=>'success','data'=>$collection],200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
       
           
            'feature'=> 'required',
            'duration'=>'required',
            'name'=>'required|max:255',
            'category'=>'required',
            'province'=>'required',
            'city'=>'required',
            'address'=>'required',
            'kode_pos'=>'required',
            'phone'=>'required',
            'email'=>'email|required|unique:company,email',
            'method'=>'required|in:transfer,cash,va',
            
           
        ],[
            'feature.required'=>'Feature is required',
            'duration.required'=>'Duration is required',
            'name.required'=>'Name of company is required',
            'name.max'=>'Name of company is maximal 255 character',
            'province.required'=>'Province is required',
            'city.required'=>'City is required',
            'address.required'=>'Address is required',
            'kode_pos.required'=>'Post code is required',
            'phone.required'=>'Phone is required',
            'email.email'=>'Email not valid',
            'email.required'=>'Email is required',
            'email.unique'=>'Email has been used',
            'category.required'=>'Category is required',
            'method.required'=>'Payment method is required',
            'method.in'=>'Payment method not matches',
          
            
        ]);
        if($validator->fails()){
            $message = $validator->errors()->all();
            $msg = [];
            foreach($message as $mess => $arr){
                $msg[] = [$message[$mess]];
            }
            return response()->json(['message'=>$msg],422);
        }else{
            $fitur = $request->feature;
            $price = $request->duration;
            $name = $request->name;
            $province = $request->province;
            $city = $request->city;
            $address = $request->address;
            $kode_pos = $request->kode_pos;
            $phone = $request->phone;
            $email = $request->email;
            $category = $request->category;
            $method = $request->method;
            $referal = $request->referal;

            $feature = Fitur::where('slug',$fitur)->first();
            if($feature){
                $fPrice = Fitur_price::where('fitur_id',$feature->id)->where('slug',$price)->first();
                if($fPrice){
                    if($fPrice->discount > 0){
                        $amount = $fPrice->price-$fPrice->discount;
                    }else{
                        $amount = $fPrice->price;
                    }
                    $city_sel = City::where('province_id',$province)->where('id',$city)->first();
                    if($city_sel){
                        $cat = Category::where('name',$category)->first();
                        if($cat){
                            $cat_id = $cat->id;
                        }else{
                            $cats = new Category();
                            $cats->name = $category;
                            $cats->save();
                            $cat_id = $cats->id;
                        }
                        if(!empty($referal)){
                            $cek = Company::where('referal_code',$referal)->first();
                            if($cek){
                                $file = $request->file('icon');
                            
                                if($request->hasfile('icon')){
                                    $validator = Validator::make($request->all(), [
    
                                        'icon' => 'required|mimes:jpeg,png,jpg|max:5048',
                                    
                                    ],[
                                        
                                        'icon.required'=>'Icon is required',
                                        'icon.mimes'=>'Icon can format jpeg,png,jpg',
                                        'icon.max'=>'Icon maximal 5mb',
                                    
                                    ]);
                                    if($validator->fails()){
                                        $message = $validator->errors()->all();
                                        $msg = [];
                                        foreach($message as $mess => $arr){
                                            $msg[] = [$message[$mess]];
                                        }
                                        return response()->json(['message'=>$msg],422);
                                    }else{
                                        $filenames = Str::slug($name). '.' . $file->getClientOriginalExtension();
                        
                                        $image =  Image::make($file);
                                        $image->resize(500, null, function ($constraint) {
                                        $constraint->aspectRatio();
                                        });  
                                        $image->save( public_path('/uploads/company/' . $filenames , 20) );
                                        $comp = new Company();
                                        $comp->fitur_id = $feature->id;
                                        $comp->fitur_price_id = $fPrice->id;
                                        $comp->referal_code = $this->generateUniqueCode();
                                        $comp->name = $name;
                                        $comp->phone = $phone;
                                        $comp->email = $email;
                                        $comp->category_id = $cat_id;
                                        $comp->address = $address;
                                        $comp->province_id = $province;
                                        $comp->icon = $filenames;
                                        $comp->city_id = $city;
                                        $comp->kode_pos = $kode_pos;
                                        $comp->active = 'pending';
                                        $comp->referal = $referal;
                                    
                                        $comp->save();
    
                                        if($comp){
                                            $pay = new Company_payment();
                                            $pay->fitur_id = $feature->id;
                                            $pay->fitur_price_id = $fPrice->id;
                                            $pay->company_id = $comp->id;
                                            $pay->invoice_no = $this->generateInvoice($name,$feature->name,$fPrice->duration);
                                            $pay->date = Carbon::now();
                                            $pay->method = $method;
                                            $pay->method_by = null;
                                            $pay->amount = $amount; 
                                        
                                            $pay->status = 'pending';
                                                
                                            
                                            $pay->save();
                                            LogActivity::addToLog('ADD COMPANY');
    
                                        }
                                        return response()->json(['message'=>'success','slug'=>$comp->slug],200);
                                    }
                                }else{
                                    $comp = new Company();
                                    $comp->fitur_id = $feature->id;
                                    $comp->fitur_price_id = $fPrice->id;
                                    $comp->referal_code = $this->generateUniqueCode();
                                    $comp->name = $name;
                                    $comp->phone = $phone;
                                    $comp->email = $email;
                                    $comp->category_id = $cat_id;
                                    $comp->address = $address;
                                    $comp->province_id = $province;
                                    $comp->icon = null;
                                    $comp->city_id = $city;
                                    $comp->kode_pos = $kode_pos;
                                    $comp->active = 'pending';
                                    $comp->referal = $referal;
                                
                                    $comp->save();

                                    if($comp){
                                        $pay = new Company_payment();
                                        $pay->fitur_id = $feature->id;
                                        $pay->fitur_price_id = $fPrice->id;
                                        $pay->company_id = $comp->id;
                                        $pay->invoice_no = $this->generateInvoice($name,$feature->name,$fPrice->duration);
                                        $pay->date = Carbon::now();
                                        $pay->method = $method;
                                        $pay->method_by = null;
                                        $pay->amount = $amount; 
                                    
                                        $pay->status = 'pending';
                                            
                                        
                                        $pay->save();
                                        LogActivity::addToLog('ADD COMPANY');

                                    }
                                    return response()->json(['message'=>'success','slug'=>$comp->slug],200);

                                }
                                
                               
                            }else{
                                return response()->json(['message'=>'Referal code not found'],404);
                            }
                        }else{
                            $file = $request->file('icon');
                            
                            if($request->hasfile('icon')){
                                $validator = Validator::make($request->all(), [
  
                                    'icon' => 'required|mimes:jpeg,png,jpg|max:5048',
                                  
                                ],[
                                    
                                    'icon.required'=>'Icon is required',
                                    'icon.mimes'=>'Icon can format jpeg,png,jpg',
                                    'icon.max'=>'Icon maximal 5mb',
                                   
                                ]);
                                if($validator->fails()){
                                    $message = $validator->errors()->all();
                                    $msg = [];
                                    foreach($message as $mess => $arr){
                                        $msg[] = [$message[$mess]];
                                    }
                                    return response()->json(['message'=>$msg],422);
                                }else{
                                    $filenames = Str::slug($name). '.' . $file->getClientOriginalExtension();
                     
                                    $image =  Image::make($file);
                                    $image->resize(500, null, function ($constraint) {
                                    $constraint->aspectRatio();
                                    });             
                                    $image->save( public_path('/uploads/company/' . $filenames , 20) );
                                    $comp = new Company();
                                    $comp->fitur_id = $feature->id;
                                    $comp->fitur_price_id = $fPrice->id;
                                    $comp->referal_code = $this->generateUniqueCode();
                                    $comp->name = $name;
                                    $comp->phone = $phone;
                                    $comp->email = $email;
                                    $comp->category_id = $cat_id;
                                    $comp->address = $address;
                                    $comp->province_id = $province;
                                    $comp->icon = $filenames;
                                    $comp->city_id = $city;
                                    $comp->kode_pos = $kode_pos;
                                    $comp->active = 'pending';
                                  
                                    $comp->referal = null;
                                    $comp->save();
        
                                    if($comp){
                                        $pay = new Company_payment();
                                        $pay->fitur_id = $feature->id;
                                        $pay->fitur_price_id = $fPrice->id;
                                        $pay->company_id = $comp->id;
                                        $pay->invoice_no = $this->generateInvoice($name,$feature->name,$fPrice->duration);
                                        $pay->date = Carbon::now();
                                        $pay->method = $method;
                                        $pay->method_by = null;
                                        $pay->amount = $amount; 
                                       
                                        $pay->status = 'pending';
                                        
                                        
                                        $pay->save();
                                        LogActivity::addToLog('ADD COMPANY');
                                    }
                                    return response()->json(['message'=>'success','slug'=>$comp->slug],200);
    
                                }
                            }else{
                                $comp = new Company();
                                $comp->fitur_id = $feature->id;
                                $comp->fitur_price_id = $fPrice->id;
                                $comp->referal_code = $this->generateUniqueCode();
                                $comp->name = $name;
                                $comp->phone = $phone;
                                $comp->email = $email;
                                $comp->category_id = $cat_id;
                                $comp->address = $address;
                                $comp->province_id = $province;
                                $comp->icon = null;
                                $comp->city_id = $city;
                                $comp->kode_pos = $kode_pos;
                                $comp->active = 'pending';
                              
                                $comp->referal = null;
                                $comp->save();
    
                                if($comp){
                                    $pay = new Company_payment();
                                    $pay->fitur_id = $feature->id;
                                    $pay->fitur_price_id = $fPrice->id;
                                    $pay->company_id = $comp->id;
                                    $pay->invoice_no = $this->generateInvoice($name,$feature->name,$fPrice->duration);
                                    $pay->date = Carbon::now();
                                    $pay->method = $method;
                                    $pay->method_by = null;
                                    $pay->amount = $amount; 
                                   
                                    $pay->status = 'pending';
                                    LogActivity::addToLog('ADD COMPANY');
                                    
                                    $pay->save();
                                }
                                return response()->json(['message'=>'success','slug'=>$comp->slug],200);
                            }
                            
                        }
                        

                    }else{
                        return response()->json(['message'=>'City or province not found'],404);
                    }
                   
                    
                }else{
                    return response()->json(['message'=>'Feature duration not found'],404);
                }
            }else{
                return response()->json(['message'=>'Feature not found'],404);
            }
        }
    }
    public function detail($slug){
        $row = Company::where('slug',$slug)->first();
        if($row){
            $collection = CompanyResource::make($row);
            return response()->json(['message'=>'success','data'=>$collection],200);
        }else{
            return response()->json(['message'=>'Company not found'],404);
        }
    }
    public function payment($slug){
        $row = Company::where('slug',$slug)->first();
        if($row){
            $payment = Company_payment::where('company_id',$row->id)->where('status','pending')->first();
            if($payment){
                $fPrice = $row->price->duration;
                if($fPrice == 'week'){
                  $row->expiry_on = date('Y-m-d H:i:s',strtotime('+7 Days'));
              }else if($fPrice == 'monthly'){
                  $row->expiry_on = date('Y-m-d H:i:s',strtotime('+7 Days'));
              }else if($fPrice == 'daily'){
                  $row->expiry_on = date('Y-m-d H:i:s',strtotime('+1 Days'));
              }else if($fPrice == 'yearly'){
                  $row->expiry_on = date('Y-m-d H:i:s',strtotime('+365 Days'));
              }
              $row->active = 'active';
              $row->update();
  
  
             
              $payment->status = 'done';
              $payment->update();
              if($row->referal != null){
                    $refCom = Company::where('referal_code',$row->referal)->first();
                    if($refCom){
                        $expiry = date('Y-m-d H:i:s',strtotime($refCom->expiry_on,strtotime('+7 Days')));
                        $ref = new Company_referal();
                        $ref->company_from = $row->id;
                        $ref->company_to = $refCom->id;
                        $ref->expiry_from = $refCom->expiry_on;
                        $ref->expiry_to = $expiry;
                        $ref->save();

                        $refCom->expiry_on = $expiry;
                        $refCom->save();
                        
                    }
              }
              LogActivity::addToLog('PAYMENT COMPANY');
              return response()->json(['message'=>'Payment successfully'],200);
            }else{
                return response()->json(['message'=>'This company not have payment'],404);
            }
           
           

        }else{
            return response()->json(['message'=>'Company not found'],404);
        }
      
    }
    private function generateInvoice($name,$paket,$price){
        $string = '';
        $string = $this->getNameInitials($name).strtoupper($paket.$price).date('dm');
        if (Company_payment::where('invoice_no', $string)->exists()) {
            $this->generateInvoice($name,$paket,$price);
        }
        return $string;
    }
    private function generateUniqueCode()
    {

        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersNumber = strlen($characters);
        $codeLength = 6;

        $code = '';

        while (strlen($code) < 6) {
            $position = rand(0, $charactersNumber - 1);
            $character = $characters[$position];
            $code = $code.$character;
        }

        if (Company::where('referal_code', $code)->exists()) {
            $this->generateUniqueCode();
        }

        return $code;

    }
    private function getNameInitials($name)
    {
       
        
        $name_array = explode(' ',trim($name));
    
        $firstWord = $name_array[0];
        $lastWord = $name_array[count($name_array)-1];
    
        return $firstWord[0]."".$lastWord[0];
    }
    
 
}
