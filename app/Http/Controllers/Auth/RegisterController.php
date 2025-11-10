<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EndUser\DashboardController;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{


    use RegistersUsers;
    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data, $roleList)
    {
        return Validator::make($data,  [
            'tp_name' => ['required', 'string', 'max:255'],
            'spoc_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:10', 'regex:/^[6-9][0-9]*$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'user_type' => ['required', 'in:' . $roleList],
            // 'g-recaptcha-response' => ['required'], // recaptcha validation
        ]);
    }


    public function register(Request $request)
    {
        $endUserRoles = Role::where('group_type', "enduser")->pluck('name')->toArray();
        $roleList = implode(',', $endUserRoles);

        $this->validator($request->all(), $roleList)->validate();

        if (in_array($request->user_type, $endUserRoles)) {
            $prefix = strtoupper(substr($request->user_type, 0, 2));
            do {
                $username = $prefix . rand(10000000, 99999999);
            } while (User::where('username', $username)->exists());

            if (User::where('email', $request->email)->exists()) {
                return redirect('/register')->with('message', 'User with this email already exists.');
            }

            try {
                DB::beginTransaction();

                $user = new User();
                $user->username = $username;
                $user->name = $request->tp_name;
                $user->spoc_name = $request->spoc_name;
                $user->phone = $request->phone;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->role = $request->user_type;
                $user->save();

                if($user){
                    $role = Role::where('name', $request->user_type)->where('group_type', 'enduser')->first();
                    if ($role) {
                        $user->assignRole($role);
                    }
                }

                $dashboard = new DashboardController();
                $dashboard->sendEmail($request->email, 'registration', ['tpDId' => $username]);
                DB::commit();

                return redirect('/login')->with('success', 'Registration successful!');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Registration failed: ' . $e->getMessage());
                return redirect('/register')->with('message', 'Registration failed. Please try again.');
            }
        }
        return redirect('/register')->with('message', 'Registration type not supported.');
    }
}
