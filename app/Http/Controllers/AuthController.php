<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
       public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // ❌ टेस्ट: गलत पासवर्ड या गलत ईमेल की जांच
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Authentication Failed. Wrong password or invalid credentials standard verification failed.'
            ], 401);
        }

        // ❌ टेस्ट: इएनएक्टिव कर्मचारी लॉगिन नहीं कर सकता
        if ($user->status === 'Inactive') {
            return response()->json([
                'message' => 'Access Denied. Your employee profile state is currently Inactive. Please contact the Master Admin.'
            ], 403);
        }

        // 🔑 टोकन जेनरेशन (Session Expiry के लिए टोकन में टाइमस्टैम्प सिंक होता है)
        $token = $user->createToken('hrms_auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status
            ]
        ], 200);
    }

        public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        // पब्लिक रजिस्ट्रेशन से बनने वाला हर यूजर सुरक्षा के लिए केवल 'Employee' रोल का होगा
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'Employee', 
        ]);

        return response()->json([
            'message' => 'Employee registered successfully inside MySQL database',
            'user' => $user
        ], 201);
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }
}
