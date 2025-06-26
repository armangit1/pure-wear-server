<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function authenticat(Request $request)
    {

        $validetor = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validetor->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validetor->errors()
            ], 400);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            $user = User::find(Auth::user()->id);

            if ($user->role === 'admin') {

                $token = $user->createToken('token')->plainTextToken;
                    
                return response()->json([
                    'status' => 200,
                    'token' => $token
                ], 200);
            } else {

                return response()->json([
                    'status' => 401,
                    'message' => 'you are not admin'
                ], 401);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'email or password is incorrect'
            ], 401);
        }
    }


 
}
