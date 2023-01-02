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
use Image;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\UserResource;
use App\Models\Company_referal;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    public function index(){
      $company = Company::orderBy('id','desc')->paginate(6);
        $collection = CompanyResource::collection($company)->response()->getData(true);
        return response()->json(['message'=>'success','data'=>$collection,'statusCode'=>200,'status'=>'success']);
    }
    public function editUser($slug,$email){
        $row = Company::where('slug',$slug)->first();
        if($row){
            $user = User::where('company_id',$row->id)->where('email',$email)->first();
            if($user){
                $collection =new UserResource($user);
                return response()->json(['message'=>'User found','user'=>$collection,'statusCode'=>200,'status'=>'ok']);
                
            }else{
                return response()->json(['message'=>'User not found','statusCode'=>404,'status'=>'error']);

            }
        }else{
            return response()->json(['message'=>'Feature not found','statusCode'=>404,'status'=>'error']);
        }
    }
    public function updateUser(Request $request,$slug,$email){
        $row = Company::where('slug',$slug)->first();
        if($row){
            $user = User::where('company_id',$row->id)->where('email',$email)->first();
            if($user){
                $validator = Validator::make($request->all(), [
       
           
                    'name'=> 'required|max:255|min:3',
                    'email'=> 'required|max:255|min:10|unique:users,email,'.$user->id.',id|email',
                  
                    'level'=>'required|in:cashier,owner',
                    'phone'=>'required|max:25',
                    'nickname'=>'required|max:255|min:3',
                   
                ],[
                    'name.required'=>'Name is required',
                    'name.max'=>'Name maximal 255 character',
                    'name.min'=>'Name minimal 3 character',
                    'email.required'=>'Email is required',
                    'email.email'=>'Email not valid',
                    'email.unique'=>'Email has been used',
                    'level.required'=>'Level is required',
                    'level.in'=>'Level not match',
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
                    $user->name = $request->name;
                    $user->email = $request->email;
                    if(!empty($password)){
                        $user->password = bcrypt($request->password);
    
                    }
                    $user->level = $request->level;
                    $user->nickname = $request->nickname;
                    $user->phone = $request->phone;
                    $user->update();
                    LogActivity::addToLog('UPDATE USER COMPANY');
                    return response()->json(['message'=>'Success updated users','status'=>'ok','statusCode'=>200]);
                }
                
            }else{
                return response()->json(['message'=>'User not found','statusCode'=>404,'status'=>'error']);

            }
        }else{
            return response()->json(['message'=>'Feature not found','statusCode'=>404,'status'=>'error']);
        }
    }
    public function addUser(Request $request,$slug){
        $row = Company::where('slug',$slug)->first();
        if($row){
            $validator = Validator::make($request->all(), [
       
                'name'=> 'required|max:255|min:3',
                'email'=>'required|unique:users,email|email',
                'password'=>'required|min:6',
                'level'=>'required|in:cashier,owner',
                'phone'=>'required|max:25',
                'nickname'=>'required|max:255|min:3',
                
               
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
                'phone.required'=>'Phone is required',
                'phone.max:25'=>'Phone maximal 25 characters',
                'nickname.required'=>'Nickname is required',
                'nickname.min'=>'Nickname min 3 characters',
                'nickname.max'=>'Nickname max 255 characters',
              
                
            ]);
            if($validator->fails()){
                $message = $validator->errors();
              
                return response()->json(['message'=>$message,'status'=>'validations','statusCode'=>422]);
            }else{
                $user = new User();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->password = bcrypt($request->password);
                $user->level = $request->level;
                $user->nickname = $request->nickname;
                $user->phone = $request->phone;
                $user->company_id = $row->id;
                $user->save();
                LogActivity::addToLog('INSERT USER COMPANY');

                return response()->json(['message'=>'Success created users','status'=>'ok','statusCode'=>200]);
            }
        }else{
            return response()->json(['status'=>'not found','statusCode'=>404,'message'=>'Company not found']);
        }
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
            $message = $validator->errors();
          
            return response()->json(['message'=>$message,'status'=>'validations','statusCode'=>422]);
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
                                        $message = $validator->errors();
                                        return response()->json(['message'=>$message,'status'=>'validations','statusCode'=>422]);
                                    }else{
                                        $filenames = Str::slug($name). '.' . $file->getClientOriginalExtension();
                        
                                        $image =  Image::make($file->getRealPath());
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
                                        $duration = $fPrice->duration;
                                        if($duration == 'week'){
                                            $comp->expiry_on = date('Y-m-d H:i:s',strtotime('+7 Days'));
                                        }else if($duration == 'monthly'){
                                            $comp->expiry_on = date('Y-m-d H:i:s',strtotime('+7 Days'));
                                        }else if($duration == 'daily'){
                                            $comp->expiry_on = date('Y-m-d H:i:s',strtotime('+1 Days'));
                                        }else if($duration == 'yearly'){
                                            $comp->expiry_on = date('Y-m-d H:i:s',strtotime('+365 Days'));
                                        }
                                    
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
                                        return response()->json(['message'=>'Success created data company','slug'=>$comp->slug,'status'=>'success','statusCode'=>200]);
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
                                    return response()->json(['message'=>'Success created data company','slug'=>$comp->slug,'status'=>'success','statusCode'=>200]);


                                }
                                
                               
                            }else{
                                return response()->json(['message'=>'Referal code not found','statusCode'=>404,'status'=>'error']);
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
                                   
                                    return response()->json(['message'=>$message,'status'=>'validations','statusCode'=>422]);
                                }else{
                                    $filenames = Str::slug($name). '.' . $file->getClientOriginalExtension();
                     
                                    $image =  Image::make($file->getRealPath());
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
                                    $duration = $fPrice->duration;
                                    if($duration == 'week'){
                                        $comp->expiry_on = date('Y-m-d H:i:s',strtotime('+7 Days'));
                                    }else if($duration == 'monthly'){
                                        $comp->expiry_on = date('Y-m-d H:i:s',strtotime('+7 Days'));
                                    }else if($duration == 'daily'){
                                        $comp->expiry_on = date('Y-m-d H:i:s',strtotime('+1 Days'));
                                    }else if($duration == 'yearly'){
                                        $comp->expiry_on = date('Y-m-d H:i:s',strtotime('+365 Days'));
                                    }
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
                                    return response()->json(['message'=>'Success created data company','slug'=>$comp->slug,'status'=>'success','statusCode'=>200]);
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
                                $duration = $fPrice->duration;
                                if($duration == 'week'){
                                    $comp->expiry_on = date('Y-m-d H:i:s',strtotime('+7 Days'));
                                }else if($duration == 'monthly'){
                                    $comp->expiry_on = date('Y-m-d H:i:s',strtotime('+7 Days'));
                                }else if($duration == 'daily'){
                                    $comp->expiry_on = date('Y-m-d H:i:s',strtotime('+1 Days'));
                                }else if($duration == 'yearly'){
                                    $comp->expiry_on = date('Y-m-d H:i:s',strtotime('+365 Days'));
                                }
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
                                return response()->json(['message'=>'Success created data company','slug'=>$comp->slug,'status'=>'success','statusCode'=>200]);

                            }
                            
                        }
                        

                    }else{
                        
                        return response()->json(['message'=>'City or province not found','statusCode'=>404,'status'=>'error']);

                    }
                   
                    
                }else{
                    return response()->json(['message'=>'Feature duration not found','statusCode'=>404,'status'=>'error']);

                   
                }
            }else{
                return response()->json(['message'=>'Feature not found','statusCode'=>404,'status'=>'error']);

              
            }
        }
    }
    public function update(Request $request,$slug){
        $row = Company::where('slug',$slug)->first();
        if($row){
            $validator = Validator::make($request->all(), [
       
           
              
                'name'=>'required|max:255',
                'category'=>'required',
                'province'=>'required',
                'city'=>'required',
                'address'=>'required',
                'kode_pos'=>'required',
                'phone'=>'required',
                'email'=>'email|required|unique:company,email,'.$row->id.',id',
             
                
               
            ],[
               
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
               
              
                
            ]);
            if($validator->fails()){
                $message = $validator->errors();
              
                return response()->json(['message'=>$message,'status'=>'validations','statusCode'=>422]);
            }else{
                $file = $request->file('icon');
                $name = $request->name;
                $province = $request->province;
                $city = $request->city;
                $address = $request->address;
                $kode_pos = $request->kode_pos;
                $phone = $request->phone;
                $email = $request->email;
                $category = $request->category;
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
                    if($request->hasfile('icon')){
                        $validator = Validator::make($request->all(), [
    
                            'icon' => 'required|mimes:jpeg,png,jpg|max:5048',
                        
                        ],[
                            
                            'icon.required'=>'Icon is required',
                            'icon.mimes'=>'Icon can format jpeg,png,jpg',
                            'icon.max'=>'Icon maximal 5mb',
                        
                        ]);
                        if($validator->fails()){
                            $message = $validator->errors();
                            return response()->json(['message'=>$message,'status'=>'validations','statusCode'=>422]);
                        }else{
                            $filenames = $row->slug. '.' . $file->getClientOriginalExtension();
            
                            $image =  Image::make($file->getRealPath());
                            $image->resize(500, null, function ($constraint) {
                            $constraint->aspectRatio();
                            });  
                            $image->save( public_path('/uploads/company/' . $filenames , 20) );
                            $row->name = $name;
                            $row->phone = $phone;
                            $row->email = $email;
                            $row->category_id = $cat_id;
                            $row->province_id = $province;
                            $row->city_id = $city;
                            $row->kode_pos = $kode_pos;
                            $row->address = $address;
                            $row->icon = $filenames;
                            $row->update();
                            LogActivity::addToLog('EDIT COMPANY');
                            return response()->json(['message'=>$row->name.' successfully updated','status'=>'success','statusCode'=>200,'slug'=>$row->slug]);
                        }
                    }else{
                        $row->name = $name;
                        $row->phone = $phone;
                        $row->email = $email;
                        $row->category_id = $cat_id;
                        $row->province_id = $province;
                        $row->city_id = $city;
                        $row->kode_pos = $kode_pos;
                        $row->address = $address;
                        $row->update();
                        LogActivity::addToLog('EDIT COMPANY');
                        return response()->json(['message'=>$row->name.' successfully updated','status'=>'success','statusCode'=>200,'slug'=>$row->slug]);
                    }
                
                }else{
                    return response()->json(['message'=>'City or Province not found','status'=>'notfound','statusCode'=>404]);

                }
            }
            
        }else{
            return response()->json(['message'=>'Company not found','status'=>'notfound','statusCode'=>404]);
        }
    }
    public function detail($slug){
        $row = Company::where('slug',$slug)->first();
        if($row){
            $collection = CompanyResource::make($row);
            return response()->json(['message'=>'success','data'=>$collection,'status'=>'success','statusCode'=>200]);
        }else{
            return response()->json(['message'=>'Company not found','status'=>'notfound','statusCode'=>404]);
        }
    }
    public function payment($slug){
        $row = Company::where('slug',$slug)->first();
        if($row){
            $payment = Company_payment::where('company_id',$row->id)->where('status','pending')->first();
            if($payment){
           
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
              return response()->json(['message'=>'Payment successfully','status'=>'success','statusCode'=>200]);
            }else{
                return response()->json(['message'=>'This company not have payment','status'=>'error','statusCode'=>201]);
            }
           
           

        }else{
            return response()->json(['message'=>'Company not found','statusCode'=>404,'status'=>'notfound']);
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
