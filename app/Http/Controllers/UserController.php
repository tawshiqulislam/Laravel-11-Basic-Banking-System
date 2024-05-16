<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function createForm()
    {
        return view('users.create');
    }
    public function storeUser(Request $request)
    {
        // validate form data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'account_type' => 'required|in:individual,business',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        // user create
        $user = User::create([
            'name' => $validatedData['name'],
            'account_type' => $validatedData['account_type'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
        ]);

        return redirect()->route('loginForm')->with('success', 'User created successfully. Please log in.');
    }
    public function loginForm()
    {
        return view('users.login');
    }
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Authentication passed
            return redirect()->intended('dashboard');
        } else {
            // Authentication failed
            return redirect()->back()->with('error', 'Invalid credentials. Please try again.');
        }
    }

    public function dashboard()
    {
        $transactions = Auth::user()->transactions()->orderBy('date', 'desc')->get();
        return view('frontend.dashboard', ['transactions' => $transactions]);
    }
}
