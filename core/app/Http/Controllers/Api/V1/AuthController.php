<?php

namespace App\Http\Controllers\Api\V1;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLogin;
use App\Models\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Mobile App Login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
            'device_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => __('Validation failed'),
                'errors' => $validator->errors()
            ], 422);
        }

        $loginField = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [$loginField => $request->username, 'password' => $request->password];

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => __('Invalid credentials')
            ], 401);
        }

        $user = Auth::user();
        
        if ($user->status == Status::USER_BAN) {
            Auth::logout();
            return response()->json([
                'status' => 'error',
                'message' => __('Your account has been banned.')
            ], 403);
        }

        $deviceName = $request->device_name ?: $request->header('User-Agent', 'Mobile App');
        $token = $user->createToken($deviceName)->plainTextToken;

        $this->createLoginLog($user);

        return response()->json([
            'status' => 'success',
            'message' => __('Login successful'),
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    /**
     * Mobile App Registration
     */
    public function register(Request $request)
    {
        $general = gs();
        $passwordValidation = Password::min(6);
        if ($general->secure_password) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:100',
            'lastname' => 'nullable|string|max:100',
            'email' => 'required|string|email|max:50|unique:users',
            'mobile' => 'required|regex:/^([0-9]*)$/|unique:users',
            'username' => 'required|string|min:6|max:30|unique:users|regex:/^[a-z0-9_]+$/',
            'password' => ['required', 'confirmed', $passwordValidation],
            'country_code' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => __('Validation failed'),
                'errors' => $validator->errors()
            ], 422);
        }

        $user = new User();
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname ?: '';
        $user->email = strtolower($request->email);
        $user->password = Hash::make($request->password);
        $user->username = strtolower($request->username);
        $user->mobile = $request->mobile;
        $user->country_code = $request->country_code;
        $user->ref_by = 0;
        $user->status = Status::USER_ACTIVE;
        $user->ev = $general->ev ? Status::UNVERIFIED : Status::VERIFIED;
        $user->sv = $general->sv ? Status::UNVERIFIED : Status::VERIFIED;
        $user->ts = 0;
        $user->tv = 1;
        $user->profile_complete = Status::YES;
        
        $user->address = [
            'address' => '',
            'state' => '',
            'zip' => '',
            'country' => '',
            'city' => '',
        ];
        
        $user->save();

        // Admin Notification
        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'New member registered via Mobile API';
        $adminNotification->click_url = urlPath('admin.users.detail', $user->id);
        $adminNotification->save();

        $deviceName = $request->device_name ?: $request->header('User-Agent', 'Mobile App');
        $token = $user->createToken($deviceName)->plainTextToken;

        $this->createLoginLog($user);

        return response()->json([
            'status' => 'success',
            'message' => __('Registration successful'),
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 201);
    }

    protected function createLoginLog($user)
    {
        $ip = getRealIP();
        $userAgent = osBrowser();
        
        $userLogin = new UserLogin();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip = $ip;
        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os = @$userAgent['os_platform'];
        
        $info = json_decode(json_encode(getIpInfo()), true);
        $userLogin->longitude = @implode(',', $info['long'] ?? []);
        $userLogin->latitude = @implode(',', $info['lat'] ?? []);
        $userLogin->city = @implode(',', $info['city'] ?? []);
        $userLogin->country_code = @implode(',', $info['code'] ?? []);
        $userLogin->country = @implode(',', $info['country'] ?? []);
        
        $userLogin->save();
    }
}
