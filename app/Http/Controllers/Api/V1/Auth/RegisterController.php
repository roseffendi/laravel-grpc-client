<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\RegisterUser;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);

        RegisterUser::dispatchNow($request->all());

        return response()->json("User created succesfully");
    }
}