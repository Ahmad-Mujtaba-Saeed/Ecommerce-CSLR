<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function index(Request $request)
    {
        return response()->json($request->user());
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string|min:4',
            'password' => 'required|string|min:4|confirmed',
        ]);
        
        $user = $request->user();
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'message' => 'Old password is incorrect',
            ], 401);
        }
        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json([
            'message' => 'Password changed successfully',
            'user' => $user,
        ]);
    }
}
