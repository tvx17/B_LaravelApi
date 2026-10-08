<?php

namespace App\Http\Responses\Fortify;

use App\Enums\Api\UserResponseCode;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    use ApiResponse;

    public function toResponse($request): JsonResponse
    {
        return $this->success(UserResponseCode::LoginSuccessful, Auth::guard(config('fortify.guard'))->user());
    }
}
