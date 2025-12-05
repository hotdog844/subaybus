<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        // Get all users, ordered so that unverified (0) come before verified (1)
        $users = User::orderBy('is_verified', 'asc')->get();
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * Verify a user account.
     */
    public function verify(User $user)
    {
        $user->update(['is_verified' => true]);

        return back()->with('success', 'User verified successfully!');
    }
    
    /**
     * Delete a user account.
     */
    public function destroy(User $user)
    {
        $user->delete();
        
        return back()->with('success', 'User deleted successfully.');
    }
}