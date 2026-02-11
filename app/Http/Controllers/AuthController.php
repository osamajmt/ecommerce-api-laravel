<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\VerificationMail;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\VerifyEmailRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class AuthController extends Controller
{
        public function register(RegisterRequest $request) {

        $verifyCode = rand(10000,99999);

        $user = User::create([
            'user_name' => $request->user_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'verify_code' => $verifyCode,
            'approval' => false,
            'role' => 'customer'
        ]);

       Mail::to($user->email)->send(new verificationMail($verifyCode));

        return response()->json([
            'status' => 'success',
            'message' => 'Registered successfully. Verification code sent.'
        ], 201);
    }

   
    public function customerLogin(Request $request)
    {
        $loginRequest = new LoginRequest();
        $request->merge(['role' => 'customer']);
        $validated = $request->validate($loginRequest->rules());
        $user = User::where('email', $validated['email'])
                    ->where('role', $validated['role'])
                    ->first();

        if(!$user){
            throw ValidationException::withMessages([
                'email' => ['No customer account found with this email']
            ]);
        }
        if ( !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials']
            ]);
        }
         if (!$user->approval) {
            return response()->json([
                'message' => 'Please verify your email address to login',
                'status' => 'pending_verification'
            ], 403);
        }

        $token = $user->createToken('customer-token')->plainTextToken;

       return response()->json([
             'user' => new UserResource($user),
            'token' => $token,
            'status' => 'success',
            'role' => 'customer'
        ]);
    }
    public function adminLogin(Request $request)
    {
        $loginRequest = new LoginRequest();
        $request->merge(['role' => 'admin']);
        $validated = $request->validate($loginRequest->rules());
        $user = User::where('email', $validated['email'])
                    ->where('role', $validated['role'])
                    ->first();

        if(!$user){
            throw ValidationException::withMessages([
                'email' => ['No admin account found with this email']
            ]);
        }
        if ( !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials']
            ]);
        }
         if (!$user->approval) {
            return response()->json([
                'message' => 'Admin account requires approval',
                'status' => 'pending_approval'
            ], 403);
        }

        $token = $user->createToken('admin-token')->plainTextToken;

       return response()->json([
             'user' => new UserResource($user),
            'token' => $token,
            'status' => 'success',
            'role' => 'admin'
        ]);
    }
    public function deliveryLogin(Request $request)
    {
        $loginRequest = new LoginRequest();
        $request->merge(['role' => 'delivery']);
        $validated = $request->validate($loginRequest->rules());
        $user = User::where('email', $validated['email'])
                    ->where('role', $validated['role'])
                    ->first();
        if(!$user){
            throw ValidationException::withMessages([
                'email' => ['No delivery account found with this email']
            ]);
        }
        if ( !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials']
            ]);
        }
         if (!$user->approval) {
            return response()->json([
               'message' => 'Your delivery account is pending admin approval',
                'status' => 'pending_approval'
            ], 403);
        }

        $token = $user->createToken('delivery-token')->plainTextToken;

       return response()->json([
             'user' => new UserResource($user),
            'token' => $token,
            'status' => 'success',
            'role' => 'delivery'
        ]);
    }



    public function logout(Request $request) {
         $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
            'status'=>'success'
        ]);
    }


    public function verifyEmail(VerifyEmailRequest  $request)
    {
          $user = User::where('email', $request->email)
            ->where('verify_code', $request->verify_code)
            ->first();



         if (!$user) {
            return response()->json([
                'message' => 'Invalid verification code',
                'status'=>'failure'
            ], 400);
        }

       $user->update([
            'email_verified_at' => now(),
            'verify_code' => null,
            'approval' => 1
        ]);


            return response()->json([
            'message' => 'Email verified successfully',
            'status'=>'success'
        ]);

    }
    public function sendVerifyCode(Request $request){

        $request->validate([
            'email' => 'required|email',
        ]);
        $user = User::where('email', $request->email)->firstOrFail();

        if (!$user) {
        return response()->json([
                'status' => 'failure',
                'message' => 'Invalid email ',

            ], 401);
        }

        $verifyCode=rand(10000,99999);

        $user->update([
            'verify_code' => $verifyCode,
        ]);

        Mail::to($user->email)->send(new verificationMail($verifyCode));
        return response()->json([
            'message' => 'Verification code sent',
              'status'=>'success'
        ]);
    }
    public function resetPassword(ResetPasswordRequest $request)
    {

       $user = User::where('email', $request->email)->firstOrFail();

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'Password updated successfully',
            'status'=>'success'
        ]);

    }
  public function updateImage(Request $request)
    {

            $request->validate([
            'image' => 'required|image|max:2048'
        ]);

            $user = $request->user();

            if ($request->hasFile('image')) {
                $extension = $request->file('image')->getClientOriginalExtension();
                $imageName = time() . '.' . $extension;

                $request->file('image')->move(
                    public_path('images/users'),
                    $imageName
                );

                $user->image = $imageName;
                $user->save();}

            return response()->json([
            'message' => 'Profile image updated',
            'image' => $imageName,
            'status'=>'success'
        ]);
    }
}







