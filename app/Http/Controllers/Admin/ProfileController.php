<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('admin.settings.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'country' => 'nullable|string',
            'postal_code' => 'nullable|string|max:10',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Update user basic info
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->country = $request->country;
        $user->postal_code = $request->postal_code;

        // Upload photo
        if ($request->hasFile('photo')) {
            $photoName = time().'.'.$request->photo->extension();
            $request->photo->move(public_path('uploads/profile'), $photoName);
            $user->photo = 'uploads/profile/'.$photoName;
        }

        // Password change
        if ($request->filled('current_password')) {
            if (Hash::check($request->current_password, $user->password)) {
                $user->password = Hash::make($request->new_password);
            } else {
                return back()->with('error', 'Current password is incorrect.');
            }
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }
}
