<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', ['users' => $users]);
    }

    public function verify(User $user)
    {
        $user->update(['is_verified' => true]);
        return redirect()->route('admin.users.index')->with('success', 'User verified successfully!');
    }

    public function destroy(User $user)
    {
        // Prevent admin from deleting their own account
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
    }
}