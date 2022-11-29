<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function sendVerificationEmail(Request $request){
        if($request->user()->hasVerifiedEmail()){
            return response()->json(['message'=>'Already verified'],200);
        }else{
            $request->user()->sendEmailVerificationNotification();
            return response()->json(['status'=>'verification-link-sent'],200);
            
        }
    }

    public function verify(EmailVerificationRequest $request){
        if($request->user()->hasVerifiedEmail()){
            return response()->json(['message'=>'Already verified'],200);
        }

        if($request->user()->markEmailasVerified()){
            event(new Verified($request->user()));
        }

        return response()->json(['message'=>'Email has been verified'],200);
    }
}
