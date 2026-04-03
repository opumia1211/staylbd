<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserSavedAddress;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function profile()
    {
        $pageTitle = "Profile Setting";
        $user      = auth()->user();
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $divisionList = getDivisionList();
        $districtsByDivision = getDistrictsByDivision();
        $thanasByDistrict = getThanaListByDistrict();
        $savedAddresses = UserSavedAddress::where('user_id', $user->id)
            ->with(['division', 'district', 'thana'])
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->get();
        $general = gs();
        $canEditUsername = \Illuminate\Support\Facades\Schema::hasColumn('users', 'username_editable') && $user->username_editable == 1;
        return view($this->activeTemplate . 'user.profile_setting', compact('pageTitle', 'user', 'countries', 'divisionList', 'districtsByDivision', 'thanasByDistrict', 'savedAddresses', 'general', 'canEditUsername'));
    }

    public function submitProfile(Request $request)
    {
        $user = auth()->user();
        $rules = [
            'firstname' => 'required|string|max:100',
            'lastname'  => 'required|string|max:100',
            'email'     => 'required|email|max:100|unique:users,email,' . $user->id,
            'mobile'    => 'nullable|string|max:30|unique:users,mobile,' . $user->id,
            'username'  => 'required|string|min:6|max:40|regex:/^[a-z0-9_]+$/|unique:users,username,' . $user->id,
            'image'     => ['nullable', 'image', 'max:2048', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'whatsapp_identity' => 'nullable|string|max:32',
            'telegram_username' => 'nullable|string|max:32|regex:/^[A-Za-z0-9_]+$/',
            'contact_channel_opt_in' => 'nullable|boolean',
            'country' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'address_2' => 'nullable|string|max:500',
            'state' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:20',
            'division' => 'nullable|string|max:100',
            'thana' => 'nullable|string|max:100',
        ];
        $messages = [
            'firstname.required' => 'First name field is required',
            'lastname.required'  => 'Last name field is required',
            'email.required' => __('Email is required.'),
            'email.unique' => __('This email is already in use.'),
            'mobile.unique' => __('This mobile number is already in use.'),
            'image.max' => 'Profile image must not exceed 2MB.',
            'telegram_username.regex' => 'Telegram username may only contain letters, numbers, and underscores.',
            'username.regex' => __('Username can only contain small letters, numbers and underscore.'),
            'username.unique' => __('This username is already taken.'),
        ];

        if (function_exists('isProfileFieldEnabled')) {
            if (isProfileFieldEnabled('age')) {
                $rules['age'] = 'nullable|integer|min:13|max:120';
            }
            if (isProfileFieldEnabled('gender')) {
                $rules['gender'] = 'nullable|in:male,female,other';
            }
            if (isProfileFieldEnabled('date_of_birth')) {
                $rules['date_of_birth'] = 'nullable|date|before:today';
            }
            if (isProfileFieldEnabled('occupation')) {
                $rules['occupation'] = 'nullable|string|max:100';
            }
            if (isProfileFieldEnabled('company_name')) {
                $rules['company_name'] = 'nullable|string|max:150';
            }
            if (isProfileFieldEnabled('website')) {
                $rules['website'] = 'nullable|url|max:255';
            }
            if (isProfileFieldEnabled('nid_number')) {
                $rules['nid_number'] = 'nullable|string|max:50';
            }
            if (isProfileFieldEnabled('tax_id')) {
                $rules['tax_id'] = 'nullable|string|max:50';
            }
            if (isProfileFieldEnabled('alternate_phone')) {
                $rules['alternate_phone'] = 'nullable|string|max:30';
            }
            if (isProfileFieldEnabled('preferred_language')) {
                $rules['preferred_language'] = 'nullable|string|max:40';
            }
        }

        $request->validate($rules, $messages);

        $user = auth()->user();

        $user->firstname = $request->firstname;
        $user->lastname  = $request->lastname;
        $user->email     = $request->email;
        $user->mobile    = $request->filled('mobile') ? preg_replace('/\s+/', '', $request->mobile) : null;
        $newUsername = strtolower(trim($request->username));
        if (preg_match('/^[a-z0-9_]{6,40}$/', $newUsername)) {
            $user->username = $newUsername;
        }

        if ($request->hasFile('image')) {
            try {
                $old         = $user->image;
                $user->image = fileUploader($request->image, getFilePath('userProfile'), getFileSize('userProfile'), $old);
                $path = getFilePath('userProfile');
                $fullPath = public_path($path . '/' . $user->image);
                if (file_exists($fullPath) && in_array(strtolower(pathinfo($user->image, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png'], true)) {
                    try {
                        $optimizer = app(\App\Services\ImageOptimizationService::class);
                        $webpPath = $optimizer->convertToWebP($fullPath, 85);
                        if ($webpPath && file_exists($webpPath)) {
                            @unlink($fullPath);
                            $user->image = basename($webpPath);
                        }
                    } catch (\Throwable $e) {
                        // keep original if WebP conversion fails
                    }
                }
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $user->address = [
            'address'   => $request->address,
            'address_2' => $request->address_2 ?? '',
            'state'     => $request->state ?? '',
            'zip'       => $request->zip ?? '',
            'country'   => $request->country ?? (@$user->address->country ?? ''),
            'city'      => $request->city ?? '',
            'division'  => $request->division ?? '',
            'thana'     => $request->thana ?? '',
        ];

        $user->whatsapp_identity = $request->whatsapp_identity
            ? preg_replace('/\D+/', '', $request->whatsapp_identity)
            : null;
        $user->telegram_username = $request->telegram_username
            ? ltrim(strtolower($request->telegram_username), '@')
            : null;
        $user->contact_channel_opt_in = $request->boolean('contact_channel_opt_in');

        if (function_exists('isProfileFieldEnabled')) {
            if (isProfileFieldEnabled('age')) {
                $user->age = $request->filled('age') ? (int) $request->age : null;
            }
            if (isProfileFieldEnabled('gender')) {
                $user->gender = $request->filled('gender') ? $request->gender : null;
            }
            $kyc = (array) ($user->kyc_data ?? (object) []);
            if (isProfileFieldEnabled('date_of_birth')) {
                $kyc['date_of_birth'] = $request->filled('date_of_birth') ? $request->date_of_birth : null;
            }
            if (isProfileFieldEnabled('occupation')) {
                $kyc['occupation'] = $request->filled('occupation') ? $request->occupation : null;
            }
            if (isProfileFieldEnabled('company_name')) {
                $kyc['company_name'] = $request->filled('company_name') ? $request->company_name : null;
            }
            if (isProfileFieldEnabled('website')) {
                $kyc['website'] = $request->filled('website') ? $request->website : null;
            }
            if (isProfileFieldEnabled('nid_number')) {
                $kyc['nid_number'] = $request->filled('nid_number') ? $request->nid_number : null;
            }
            if (isProfileFieldEnabled('tax_id')) {
                $kyc['tax_id'] = $request->filled('tax_id') ? $request->tax_id : null;
            }
            if (isProfileFieldEnabled('alternate_phone')) {
                $kyc['alternate_phone'] = $request->filled('alternate_phone') ? $request->alternate_phone : null;
            }
            if (isProfileFieldEnabled('preferred_language')) {
                $kyc['preferred_language'] = $request->filled('preferred_language') ? $request->preferred_language : null;
            }
            $user->kyc_data = (object) $kyc;
        }

        $user->save();
        activity_log(\App\Models\UserActivityLog::PROFILE_UPDATE, 'Profile updated', null, null);
        $notify[] = ['success', 'Profile updated successfully'];
        return back()->withNotify($notify);
    }

    public function storeSavedAddress(Request $request)
    {
        $request->validate([
            'country' => 'required|string|max:100',
            'division_id' => 'nullable|integer|exists:divisions,id',
            'district_id' => 'nullable|integer|exists:districts,id',
            'thana_id' => 'nullable|integer|exists:thanas,id',
            'postal_code' => 'nullable|string|max:20',
            'address_line' => 'required|string|max:500',
            'address_line_2' => 'nullable|string|max:500',
            'state' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'label' => 'nullable|string|max:50',
        ]);
        $user = auth()->user();
        $isFirst = $user->savedAddresses()->count() === 0;
        UserSavedAddress::create([
            'user_id' => $user->id,
            'country' => $request->country,
            'division_id' => $request->division_id,
            'district_id' => $request->district_id,
            'thana_id' => $request->thana_id,
            'postal_code' => $request->postal_code,
            'address_line' => $request->address_line,
            'address_line_2' => $request->address_line_2,
            'state' => $request->state,
            'city' => $request->city,
            'label' => $request->label,
            'is_default' => $isFirst ? 1 : 0,
        ]);
        $notify[] = ['success', __('Address saved.')];
        return back()->withNotify($notify);
    }

    public function updateSavedAddress(Request $request, $id)
    {
        $addr = UserSavedAddress::where('user_id', auth()->id())->findOrFail($id);
        $request->validate([
            'country' => 'required|string|max:100',
            'division_id' => 'nullable|integer|exists:divisions,id',
            'district_id' => 'nullable|integer|exists:districts,id',
            'thana_id' => 'nullable|integer|exists:thanas,id',
            'postal_code' => 'nullable|string|max:20',
            'address_line' => 'required|string|max:500',
            'address_line_2' => 'nullable|string|max:500',
            'state' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'label' => 'nullable|string|max:50',
        ]);
        $addr->update([
            'country' => $request->country,
            'division_id' => $request->division_id,
            'district_id' => $request->district_id,
            'thana_id' => $request->thana_id,
            'postal_code' => $request->postal_code,
            'address_line' => $request->address_line,
            'address_line_2' => $request->address_line_2,
            'state' => $request->state,
            'city' => $request->city,
            'label' => $request->label,
        ]);
        $notify[] = ['success', __('Address updated.')];
        return back()->withNotify($notify);
    }

    public function destroySavedAddress($id)
    {
        $addr = UserSavedAddress::where('user_id', auth()->id())->findOrFail($id);
        $addr->delete();
        $notify[] = ['success', __('Address removed.')];
        return back()->withNotify($notify);
    }

    public function setDefaultSavedAddress($id)
    {
        $addr = UserSavedAddress::where('user_id', auth()->id())->findOrFail($id);
        UserSavedAddress::where('user_id', auth()->id())->update(['is_default' => 0]);
        $addr->update(['is_default' => 1]);
        $notify[] = ['success', __('Default address updated.')];
        return back()->withNotify($notify);
    }

    public function changePassword()
    {
        $pageTitle = 'Change Password';
        return view($this->activeTemplate . 'user.password', compact('pageTitle'));
    }

    public function submitPassword(Request $request)
    {

        $passwordValidation = Password::min(6);
        
        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $this->validate($request, [
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', $passwordValidation],
        ]);

        $user = auth()->user();

        if (Hash::check($request->current_password, $user->password)) {
            $password       = Hash::make($request->password);
            $user->password = $password;
            $user->save();
            activity_log(\App\Models\UserActivityLog::PASSWORD_CHANGE, 'Password changed', null, null);
            $notify[] = ['success', 'Password changes successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'The password doesn\'t match!'];
            return back()->withNotify($notify);
        }
    }
}
