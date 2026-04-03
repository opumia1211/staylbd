<?php

namespace App\Http\Controllers\User\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\User;
use App\Models\UserLogin;
use App\Traits\OrderConfirmation;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use OrderConfirmation;
    use RegistersUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest');
        $this->middleware('registration.status')->except('registrationNotAllowed');
    }

    public function showRegistrationForm()
    {
        return response()->view($this->activeTemplate . 'user.auth.modal_shell', [
            'openModal' => 'register',
        ])->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $general = gs();
        $passwordValidation = Password::min(6);
        if ($general->secure_password) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }
        $agree = 'nullable';
        if ($general->agree && isRegistrationFieldEnabled('agree')) {
            $agree = 'required';
        }
        $countryData = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes = implode(',',array_column($countryData, 'dial_code'));
        $countries = implode(',',array_column($countryData, 'country'));

        $rules = [
            'firstname' => isRegistrationFieldEnabled('firstname') ? 'required|string|max:100' : 'nullable|string|max:100',
            'lastname'  => 'nullable|string|max:100',
            'email' => isRegistrationFieldEnabled('email') ? 'required|string|max:50' : 'nullable|string|max:50',
            'mobile' => isRegistrationFieldEnabled('mobile') ? 'required|regex:/^([0-9]*)$/' : 'nullable|regex:/^([0-9]*)$/',
            'username' => isRegistrationFieldEnabled('username') ? 'required|string|min:6|max:30|unique:users|regex:/^[a-z0-9_]+$/' : 'nullable|string|min:6|max:30|unique:users|regex:/^[a-z0-9_]*$/',
            'age' => isRegistrationFieldEnabled('age') ? 'required|integer|min:13|max:120' : 'nullable|integer|min:13|max:120',
            'gender' => isRegistrationFieldEnabled('gender') ? 'required|in:male,female,other' : 'nullable|in:male,female,other',
            'captcha' => isRegistrationFieldEnabled('captcha') ? 'sometimes|required' : 'nullable',
            'mobile_code' => isRegistrationFieldEnabled('mobile') ? 'required|in:'.$mobileCodes : 'nullable|in:'.$mobileCodes,
            'country_code' => isRegistrationFieldEnabled('mobile') ? 'required|in:'.$countryCodes : (isRegistrationFieldEnabled('country') ? 'required|in:'.$countryCodes : 'nullable|in:'.$countryCodes),
            'country' => isRegistrationFieldEnabled('country') ? 'required|in:'.$countries : 'nullable|in:'.$countries,
            'agree' => $agree,
            'address' => isRegistrationFieldEnabled('address') ? 'required|string|max:255' : 'nullable|string|max:255',
            'city' => isRegistrationFieldEnabled('city') ? 'required|string|max:100' : 'nullable|string|max:100',
            'state' => isRegistrationFieldEnabled('state') ? 'required|string|max:100' : 'nullable|string|max:100',
            'zip' => isRegistrationFieldEnabled('zip') ? 'required|string|max:20' : 'nullable|string|max:20',
            'division' => isRegistrationFieldEnabled('division') ? 'nullable|string|max:100' : 'nullable|string|max:100',
            'district' => isRegistrationFieldEnabled('district') ? 'nullable|string|max:100' : 'nullable|string|max:100',
            'thana' => isRegistrationFieldEnabled('thana') ? 'nullable|string|max:100' : 'nullable|string|max:100',
            'date_of_birth' => isRegistrationFieldEnabled('date_of_birth') ? 'required|date|before:today' : 'nullable|date|before:today',
            'occupation' => isRegistrationFieldEnabled('occupation') ? 'required|string|max:100' : 'nullable|string|max:100',
            'company_name' => isRegistrationFieldEnabled('company_name') ? 'required|string|max:150' : 'nullable|string|max:150',
            'website' => isRegistrationFieldEnabled('website') ? 'required|url|max:255' : 'nullable|url|max:255',
            'telegram' => 'nullable|string|max:60',
            'whatsapp' => 'nullable|string|max:30',
            'newsletter_subscribe' => 'nullable',
            'how_heard' => isRegistrationFieldEnabled('how_heard') ? 'required|in:search,friend,social,ad,other' : 'nullable|in:search,friend,social,ad,other',
            'nid_number' => isRegistrationFieldEnabled('nid_number') ? 'required|string|max:50' : 'nullable|string|max:50',
            'alternate_phone' => isRegistrationFieldEnabled('alternate_phone') ? 'required|string|max:30' : 'nullable|string|max:30',
            'preferred_language' => isRegistrationFieldEnabled('preferred_language') ? 'required|string|max:40' : 'nullable|string|max:40',
            'tax_id' => isRegistrationFieldEnabled('tax_id') ? 'required|string|max:50' : 'nullable|string|max:50',
        ];

        if (isRegistrationFieldEnabled('password')) {
            $rules['password'] = ['required','confirmed',$passwordValidation];
        } else {
            $rules['password'] = ['nullable','confirmed',$passwordValidation];
        }

        $validate = Validator::make($data, $rules, [
            'username.regex' => __('Username can only contain small letters, numbers and underscore.'),
            'username.unique' => __('This username is already taken.'),
            'email.unique' => __('This email is already registered. Please login or use a different email to create a new account.'),
        ]);

        // Email field may be email or mobile number
        if (isRegistrationFieldEnabled('email') && !empty($data['email'])) {
            $validate->after(function ($validator) use ($data, $mobileCodes) {
                $emailVal = trim((string) $data['email']);
                if (str_contains($emailVal, '@')) {
                    if (!filter_var($emailVal, FILTER_VALIDATE_EMAIL)) {
                        $validator->errors()->add('email', __('Please enter a valid email address.'));
                        return;
                    }
                    if (User::where('email', strtolower($emailVal))->exists()) {
                        $validator->errors()->add('email', __('This email is already registered. Please login or use a different email to create a new account.'));
                        return;
                    }
                    if (isRegistrationFieldEnabled('mobile') && (empty($data['mobile']) && empty($data['mobile_code']))) {
                        $validator->errors()->add('mobile', __('Mobile number is required when using email.'));
                    }
                } else {
                    $digits = preg_replace('/\D/', '', $emailVal);
                    if (strlen($digits) < 10 || strlen($digits) > 15) {
                        $validator->errors()->add('email', __('Please enter a valid mobile number (10-15 digits).'));
                        return;
                    }
                    $mobileCode = $data['mobile_code'] ?? '';
                    if (!in_array($mobileCode, array_filter(explode(',', $mobileCodes)))) {
                        $validator->errors()->add('email', __('Please select country so we can validate your mobile number.'));
                        return;
                    }
                    $fullMobile = $mobileCode . $digits;
                    if (User::where('mobile', $fullMobile)->exists()) {
                        $validator->errors()->add('email', __('This mobile number is already registered. Please login or use a different number.'));
                    }
                }
            });
        }

        // At least one of username or email must be provided when both are enabled; if one disabled, the other must be enabled
        if (!isRegistrationFieldEnabled('username') && !isRegistrationFieldEnabled('email')) {
            $validate->after(function ($validator) {
                $validator->errors()->add('email', __('Either username or email must be required. Enable at least one in Registration form fields.'));
            });
        }
        if (isRegistrationFieldEnabled('password') && empty($data['password'])) {
            $validate->after(function ($validator) {
                $validator->errors()->add('password', __('Password is required.'));
            });
        }

        // One account per mobile number: same number cannot register again
        if (isRegistrationFieldEnabled('mobile') && !empty($data['mobile']) && !empty($data['mobile_code'])) {
            $validate->after(function ($validator) use ($data) {
                $fullMobile = ($data['mobile_code'] ?? '') . ($data['mobile'] ?? '');
                if ($fullMobile !== '' && \App\Models\User::where('mobile', $fullMobile)->exists()) {
                    $validator->errors()->add('mobile', __('This number is already registered. One account per mobile number.'));
                }
            });
        }

        return $validate;
    }

    public function register(Request $request)
    {
        $wantsJson = $request->wantsJson() || $request->ajax();

        if (isRegistrationFieldEnabled('username') && $request->filled('username')) {
            $request->merge(['username' => strtolower(trim((string) $request->username))]);
        }

        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            if ($wantsJson) {
                return response()->json(['message' => __('Validation failed.'), 'errors' => $validator->errors()], 422);
            }
            return redirect()->route('user.register')
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $request->session()->regenerateToken();

        if (isRegistrationFieldEnabled('captcha') && !verifyCaptcha()) {
            if ($wantsJson) {
                return response()->json(['message' => __('Invalid captcha provided'), 'errors' => ['captcha' => [__('Invalid captcha provided')]]], 422);
            }
            $notify[] = ['error', __('Invalid captcha provided')];
            return redirect()->route('user.register')->withNotify($notify)->withInput();
        }

        if (isRegistrationFieldEnabled('mobile') && $request->filled('mobile')) {
            $exist = User::where('mobile', $request->mobile_code . $request->mobile)->first();
            if ($exist) {
                if ($wantsJson) {
                    return response()->json(['message' => __('The mobile number already exists'), 'errors' => ['mobile' => [__('The mobile number already exists')]]], 422);
                }
                $notify[] = ['error', __('The mobile number already exists')];
                return redirect()->route('user.register')->withNotify($notify)->withInput();
            }
        }

        try {
            $user = DB::transaction(function () use ($request) {
                $user = $this->create($request->all());
                event(new Registered($user));
                return $user;
            });
        } catch (\Throwable $e) {
            Log::error('User registration failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $request->input('email'),
                'username' => $request->input('username'),
            ]);
            if ($wantsJson) {
                return response()->json(['message' => __('Registration failed. Please try again or contact support.')], 422);
            }
            $notify[] = ['error', __('Registration failed. Please try again or contact support.')];
            return redirect()->route('user.register')->withNotify($notify)->withInput($request->except('password', 'password_confirmation'));
        }

        if (session()->has('cart')) {
            static::createCart($user);
        }
        if (session()->has('wishlist')) {
            static::migrateWishlist($user);
        }
        $this->guard()->login($user);

        activity_log(\App\Models\UserActivityLog::REGISTRATION, 'New user registered: ' . ($user->username ?? $user->email), null, null);

        $redirectUrl = $this->getRegistrationRedirectUrl($request);
        if ($wantsJson) {
            return response()->json(['redirect' => $redirectUrl]);
        }
        return redirect()->to($redirectUrl);
    }

    /**
     * Get redirect URL after registration (current page or intended).
     */
    protected function getRegistrationRedirectUrl(Request $request): string
    {
        $redirect = $request->input('redirect');
        if ($redirect && is_string($redirect)) {
            $url = trim($redirect);
            if ($url !== '' && \Illuminate\Support\Str::startsWith($url, url('/'))) {
                return $this->stripOpenParamFromUrl($url);
            }
        }
        $registered = $this->registered($request, $this->guard()->user());
        if ($registered && method_exists($registered, 'getTargetUrl')) {
            return $registered->getTargetUrl();
        }
        return route('user.home');
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $general = gs();

        $referBy = session()->get('reference');
        if ($referBy) {
            $referUser = User::where('username', $referBy)->first();
        } else {
            $referUser = null;
        }

        // Defaults for disabled registration fields (admin may hide some fields)
        $username = isRegistrationFieldEnabled('username') && !empty($data['username'])
            ? strtolower(trim($data['username']))
            : strtolower('user' . time() . substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(4))), 0, 6));

        $emailInput = isset($data['email']) ? trim((string) $data['email']) : '';
        $isEmailFormat = $emailInput !== '' && str_contains($emailInput, '@');
        if (isRegistrationFieldEnabled('email') && $emailInput !== '') {
            if ($isEmailFormat) {
                $email = strtolower($emailInput);
                $mobile = isRegistrationFieldEnabled('mobile') && isset($data['mobile_code'], $data['mobile'])
                    ? ($data['mobile_code'] . $data['mobile'])
                    : ('nomobile' . time() . rand(1000, 9999));
            } else {
                $digits = preg_replace('/\D/', '', $emailInput);
                $mobile = isset($data['mobile_code']) ? ($data['mobile_code'] . $digits) : ('nomobile' . time() . rand(1000, 9999));
                $host = parse_url(config('app.url'), PHP_URL_HOST) ?: 'placeholder.local';
                $email = $username . '@' . $host;
            }
        } else {
            $email = $username . '@' . (parse_url(config('app.url'), PHP_URL_HOST) ?: 'placeholder.local');
            $mobile = isRegistrationFieldEnabled('mobile') && isset($data['mobile_code'], $data['mobile'])
                ? ($data['mobile_code'] . $data['mobile'])
                : ('nomobile' . time() . rand(1000, 9999));
        }
        // DB column email is varchar(40) in many setups – ensure we do not exceed
        $email = \Illuminate\Support\Str::limit($email, 40, '');

        $password = isRegistrationFieldEnabled('password') && !empty($data['password'])
            ? $data['password']
            : \Illuminate\Support\Str::random(24);
        $countryData = (array) json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $firstCode = array_key_first($countryData);
        $firstCountry = $countryData[$firstCode]->country ?? '';
        $countryName = $data['country'] ?? (isset($data['country_code'], $countryData[$data['country_code']]) ? $countryData[$data['country_code']]->country : $firstCountry);

        $user = new User();
        $user->firstname = $data['firstname'] ?? 'User';
        $user->lastname = $data['lastname'] ?? '';
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->username = $username;
        if (Schema::hasColumn('users', 'username_editable')) {
            $user->username_editable = (isRegistrationFieldEnabled('username') && !empty(trim($data['username'] ?? ''))) ? 0 : 1;
        }
        $user->ref_by = $referUser ? $referUser->id : 0;
        $user->country_code = $data['country_code'] ?? $firstCode;
        $user->mobile = $mobile;
        $user->age = (int) ($data['age'] ?? 0);
        if (Schema::hasColumn('users', 'gender')) {
            $user->gender = isset($data['gender']) && $data['gender'] !== '' ? $data['gender'] : null;
        }
        $user->profile_complete = Status::YES;
        $user->address = [
            'address' => $data['address'] ?? '',
            'state' => $data['state'] ?? '',
            'zip' => $data['zip'] ?? '',
            'country' => $countryName,
            'city' => $data['city'] ?? '',
            'division' => $data['division'] ?? '',
            'district' => $data['district'] ?? '',
            'thana' => $data['thana'] ?? '',
        ];
        $kycData = array_filter([
            'occupation' => $data['occupation'] ?? null,
            'company_name' => $data['company_name'] ?? null,
            'website' => $data['website'] ?? null,
            'telegram' => $data['telegram'] ?? null,
            'whatsapp' => $data['whatsapp'] ?? null,
            'newsletter_subscribe' => !empty($data['newsletter_subscribe']) ? 1 : 0,
            'how_heard' => $data['how_heard'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'nid_number' => $data['nid_number'] ?? null,
            'alternate_phone' => $data['alternate_phone'] ?? null,
            'preferred_language' => $data['preferred_language'] ?? null,
            'tax_id' => $data['tax_id'] ?? null,
            // Store gender in kyc_data when users.gender column does not exist (e.g. before migration)
            'gender' => !Schema::hasColumn('users', 'gender') && isset($data['gender']) && $data['gender'] !== '' ? $data['gender'] : null,
        ], function ($v) { return $v !== null && $v !== ''; });
        $user->kyc_data = !empty($kycData) ? $kycData : (object) [];
        $user->kv = $general->kv ? Status::NO : Status::YES;
        $user->ev = $general->ev ? Status::NO : Status::YES;
        $user->sv = $general->sv ? Status::NO : Status::YES;
        $user->ts = 0;
        $user->tv = 1;
        $user->save();


        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'New member registered';
        $adminNotification->click_url = urlPath('admin.users.detail',$user->id);
        $adminNotification->save();


        //Login Log Create
        $ip = getRealIP();
        $exist = UserLogin::where('user_ip',$ip)->first();
        $userLogin = new UserLogin();

        //Check exist or not
        if ($exist) {
            $userLogin->longitude =  $exist->longitude;
            $userLogin->latitude =  $exist->latitude;
            $userLogin->city =  $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country =  $exist->country;
        }else{
            $info = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude =  @implode(',',$info['long']);
            $userLogin->latitude =  @implode(',',$info['lat']);
            $userLogin->city =  @implode(',',$info['city']);
            $userLogin->country_code = @implode(',',$info['code']);
            $userLogin->country =  @implode(',', $info['country']);
        }

        $userAgent = osBrowser();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip =  $ip;

        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os = @$userAgent['os_platform'];
        $userLogin->save();


        return $user;
    }

    public function checkUser(Request $request)
    {
        $exist = ['data' => false, 'type' => null];
        if ($request->filled('email')) {
            $exist['data'] = User::where('email', $request->email)->exists();
            $exist['type'] = 'email';
        }
        if ($request->filled('mobile')) {
            $exist['data'] = User::where('mobile', $request->mobile)->exists();
            $exist['type'] = 'mobile';
        }
        if ($request->filled('username')) {
            $username = strtolower(trim((string) $request->username));
            $exist['type'] = 'username';
            if (strlen($username) < 6) {
                $exist['data'] = false;
                $exist['valid'] = false;
            } elseif (!preg_match('/^[a-z0-9_]+$/', $username)) {
                $exist['data'] = false;
                $exist['valid'] = false;
            } else {
                $exist['data'] = User::whereRaw('LOWER(username) = ?', [$username])->exists();
                $exist['valid'] = true;
            }
        }
        return response()->json($exist);
    }

    public function registered(Request $request, $user)
    {
        $redirect = $request->input('redirect');
        if ($redirect && is_string($redirect)) {
            $url = trim($redirect);
            $homeUrl = url('/');
            if ($url !== '' && \Illuminate\Support\Str::startsWith($url, $homeUrl)) {
                $url = $this->stripOpenParamFromUrl($url);
                $registerPath = parse_url(route('user.register'), PHP_URL_PATH);
                $currentPath = parse_url($url, PHP_URL_PATH);
                if ($currentPath && $registerPath && $currentPath === $registerPath) {
                    return redirect()->to(route('user.home'));
                }
                return redirect()->to($url);
            }
        }
        return redirect()->to(route('user.home'));
    }

    /**
     * Strip open=register and open=login from URL so that after redirect the floating auth panel does not auto-open.
     */
    private function stripOpenParamFromUrl(string $url): string
    {
        $url = preg_replace('/([?&])open=(?:register|login)(?=&|$)/', '$1', $url);
        $url = preg_replace('/\?&/', '?', $url); // ?& -> ?
        return rtrim(rtrim($url, '&'), '?');
    }

}
