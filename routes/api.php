<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\FiturController;
use App\Http\Controllers\API\ResourceController;
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
    return $request->user();
});


Route::group(['middleware' => 'auth:sanctum'],function(){
    Route::get('/category',[CategoryController::class,'index']);
    Route::get('/logout',[AuthController::class,'logout']);

    Route::post('/resource/store',[ResourceController::class,'store']);
    Route::get('/resource',[ResourceController::class,'index']);
    Route::get('/resource/edit/{slug}',[ResourceController::class,'edit']);
    Route::post('/resource/update/{slug}',[ResourceController::class,'update']);
    Route::delete('/resource/delete/{slug}',[ResourceController::class,'destroy']);


    Route::get('/feature',[FiturController::class,'index']);
    Route::get('/feature/best/{slug}',[FiturController::class,'best']);
    Route::get('/feature/detail/{slug}',[FiturController::class,'detail']);
    Route::get('/feature/edit/{slug}',[FiturController::class,'edit']);
    Route::get('/feature/checkresource/{slug}/{id}',[FiturController::class,'checkResource']);
    Route::get('/feature/edit/price/{slug}/{price}',[FiturController::class,'editPrice']);
    Route::post('/feature/edit/price/{slug}/{price}',[FiturController::class,'updatePrice']);
    Route::post('/feature/edit/{slug}',[FiturController::class,'update']);
    Route::post('/feature/store',[FiturController::class,'store']);
    Route::post('/feature/store/price/{slug}',[FiturController::class,'storePrice']);
    Route::post('/feature/store/resource/{slug}',[FiturController::class,'storeResource']);
    Route::delete('/feature/delete/{slug}',[FiturController::class,'destroy']);
    Route::delete('/feature/delete/price/{slug}/{id}',[FiturController::class,'destroyPrice']);
    Route::delete('/feature/delete/resource/{slug}/{id}',[FiturController::class,'destroyResource']);




  



    
});
Route::post('/login',[AuthController::class,'login']);
