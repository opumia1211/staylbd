<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Get authenticated user profile
     */
    public function profile()
    {
        $user = Auth::user();
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user
            ]
        ]);
    }

    /**
     * Update user profile (optional, but good to have)
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
        ]);

        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => __('Profile updated successfully'),
            'data' => [
                'user' => $user
            ]
        ]);
    }
}
