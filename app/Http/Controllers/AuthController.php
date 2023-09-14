<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Sanctum;

class AuthController extends Controller
{
    use ApiResponser;
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        if (!Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            return $this->errorResponse('Unauthorized', 401, 40100);
        }

        $user = User::where('username', $request->username)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->showOne([
            'access_token' => $token,
            'name' => $user->name,
            'username' => $user->username
        ]);
    }

    public function profile(Request $request)
    {
        return $this->showOne($request->user());
    }
}
