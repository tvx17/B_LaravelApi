<?php

namespace App\Actions\Users;

use App\Enums\Api\SystemResponseCode;
use App\Enums\Api\UserResponseCode;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginAction
{
    use ApiResponse;

    public function handle(Request $request): JsonResponse
    {
        if (! $request->hasSession()) {
            return $this->error(SystemResponseCode::Forbidden, null, 403);
        }

        $credentials = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->attempt($credentials)) {
            return $this->error(UserResponseCode::CredentialsError, null, 401);
        }

        $request->session()->regenerate();

        return $this->success(UserResponseCode::LoginSuccessful, Auth::guard('web')->user());
    }
}
