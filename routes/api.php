<?php

use App\Http\Controllers\API\AjaxController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\EmailVerificationController;
use App\Http\Controllers\API\FiturController;
use App\Http\Controllers\API\ResourceController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    $user = $request->user();
    $userArr = ['email'=>$user->email,'name'=>$user->name,'level'=>$user->level,'verify'=>$user->email_verified_at];
    return $userArr;
});


Route::group(['middleware' => 'auth:sanctum'],function(){
    
    Route::post('/email/verification-notification',[EmailVerificationController::class,'sendVerificationEmail']);
    Route::get('verify-email/{id}/{hash}',[EmailVerificationController::class,'verify'])->name('verification.verify');

    Route::middleware(['auth','VerifyEmail'])->group(function () {
       
        Route::get('/category',[CategoryController::class,'index']);
        Route::get('/category/search',[CategoryController::class,'search']);
        Route::get('/province',[AjaxController::class,'getProvince']);
        Route::get('/featureall',[AjaxController::class,'getFeature']);
        
        Route::get('/city/{province}',[AjaxController::class,'getCity']);


        Route::get('/logout',[AuthController::class,'logout']);
    
        Route::post('/resource/store',[ResourceController::class,'store']);
        Route::get('/resource',[ResourceController::class,'index']);
        Route::get('/resource/get',[ResourceController::class,'get']);

        Route::get('/resource/edit/{slug}',[ResourceController::class,'edit']);
        Route::post('/resource/update/{slug}',[ResourceController::class,'update']);
        Route::delete('/resource/delete/{slug}',[ResourceController::class,'destroy']);


        Route::get('/company',[CompanyController::class,'index']);
        Route::post('/company/add',[CompanyController::class,'store']);
        Route::get('/company/detail/{slug}',[CompanyController::class,'detail']);
        Route::post('/company/payment/{slug}',[CompanyController::class,'payment']);




        Route::middleware('CheckLevel:admin')->group(function () {
      

            Route::get('/feature',[FiturController::class,'index']);
            Route::get('/feature/best/{slug}',[FiturController::class,'best']);
            Route::get('/feature/detail/{slug}',[FiturController::class,'detail']);
            Route::get('/feature/edit/{slug}',[FiturController::class,'edit']);
            Route::get('/feature/resource/detail/{slug}',[FiturController::class,'checkResource']);
            Route::get('/feature/edit/price/{slug}/{price}',[FiturController::class,'editPrice']);
            Route::post('/feature/edit/price/{slug}/{price}',[FiturController::class,'updatePrice']);
            Route::post('/feature/edit/{slug}',[FiturController::class,'update']);
            Route::post('/feature/store',[FiturController::class,'store']);
            Route::post('/feature/store/price/{slug}',[FiturController::class,'storePrice']);
            Route::post('/feature/store/resource/{slug}',[FiturController::class,'storeResource']);
            Route::delete('/feature/delete/{slug}',[FiturController::class,'destroy']);
            Route::delete('/feature/delete/price/{slug}/{id}',[FiturController::class,'destroyPrice']);
            Route::delete('/feature/delete/resource/{slug}/{id}',[FiturController::class,'destroyResource']);
    
            Route::get('/users',[UserController::class,'index']);
            Route::get('/users/edit/{email}',[UserController::class,'edit']);
            Route::get('/users/detail/{email}',[UserController::class,'detail']);
            Route::post('/users/add',[UserController::class,'store']);
            Route::post('/users/update/{email}',[UserController::class,'update']);
            Route::delete('/users/delete/{email}',[UserController::class,'destroy']);


            
    
    
    
    
        });
        Route::middleware('CheckLevel:user')->group(function () {
      
            
        });
    });
   
   


    




  



    
});
Route::post('/login',[AuthController::class,'login']);
