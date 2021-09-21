<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Response\Facade\Response;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['account', 'password']);

        if (!$token = auth('admin')->attempt($credentials)) {
            return Response::errorUnauthorized();
        }
        return Response::success(['token' => $token]);
    }
}
