<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Staff;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;


class AuthController extends Controller
{
    public function createCustomer(Request $request){

        try{
        //Validated
        $validateUser = Validator::make($request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|unique:customers,email',
                'password' => 'required'
            ]);

        if($validateUser->fails()){
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validateUser->errors()
            ], 401);
        }

        $user = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
         event(new Registered($user));


         return response()->json([
            'status' => true,
            'message' => 'User Created Successfully',
            'token' => $user->createToken("API TOKEN")->plainTextToken
        ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function login(Request $request){
        try {
            $validateUser = Validator::make($request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $user = Customer::where('email', $request->email)->first();
            if ($user->email_verified_at) {
                if(!Auth::guard('customer')->attempt($request->only(['email', 'password']))){
                    return response()->json([
                        'status' => false,
                        'message' => 'Email & Password does not match with our record.',
                    ], 401);
                }
                $token = $user->createToken("token")->plainTextToken;
                $cookie = cookie('jwt-client', $token, 60 * 24);

                return response()->json([
                    'status' => true,
                    'message' => 'User Logged In Successfully',
                ], 200)->withCookie($cookie);
            }
            return response()->json([
                'status' => false,
                'message' => 'Email not verified! Please verify your email before login',
            ], 401);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function verifyEmail($id, Request $request){
        if (!$request->hasValidSignature()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid/Expired url provided.'
            ], 401);
        }
        try {
            $user = Customer::findOrFail($id);
            if (! $user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();

                event(new Verified($user));
            }
            return response()->json([
                'status' => true,
                'message' => 'Email verified.',
            ], 200);
        }catch (\Exception $ex){
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }

    }
    public function resendVerification() {
        try {
            if (auth()->user()->hasVerifiedEmail()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email already verified.'
                ], 400);
            }
            auth()->user()->sendEmailVerificationNotification();

            return response()->json([
                'status' => true,
                'message' => 'Email verification link sent on your email id'
            ], 200);
        }catch (\Exception $ex){
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }

    public function getCurrentUser(Request $request){
        return response()->json(['data' => $request->user()], 200);
    }

    public function logout(Request $request){
        $cookie = Cookie::forget('jwt-client');
        return response()->json([
            'status' => true,
            'message' => 'Success'
        ], 200)->withCookie($cookie);
    }

}
