<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class MobileAuthController extends Controller
{
    
    public function showRegisterForm()
    {
        if (Auth::check()) {
    return redirect('/mobile/dashboard');
}
        return view('mobile_register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users',
            'phone'           => 'required|string|max:20',
            'password'        => 'required|confirmed|min:6',
            'passenger_type'  => 'required|in:regular,student,senior,pwd',
            'id_card'         => 'required|image|max:2048',
        ]);

        // ✅ Handle ID upload
        $idPath = $request->file('id_card')->store('ids', 'public');

        // ✅ Create user
        $user = User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'phone'          => $request->phone,
            'password'       => Hash::make($request->password),
            'passenger_type' => $request->passenger_type,
            'id_card_path'   => $idPath,
        ]);

        // ✅ Auto login
        Auth::login($user);

        return redirect('/mobile/dashboard');

        if (Auth::check()) return redirect('/mobile/dashboard');
    }

    public function showLoginForm()
    {
        if (Auth::check()) {
    return redirect('/mobile/dashboard');
}
        return view('mobile_login');
    }

   public function login(Request $request)
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required'
    ]);

    if (Auth::attempt($request->only('email', 'password'))) {
        return redirect('/mobile/dashboard');
    }

    return back()->withErrors(['email' => 'Invalid credentials']);
}


   public function logout()
{
    Auth::logout();
    return redirect()->route('mobile.login');
}

}
