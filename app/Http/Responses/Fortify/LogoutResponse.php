<?php

namespace App\Http\Responses\Fortify;

use App\Enums\Api\UserResponseCode;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;

class LogoutResponse implements LogoutResponseContract
{
    use ApiResponse;

    public function toResponse($request): JsonResponse
    {
        return $this->success(UserResponseCode::LogoutSuccessful);
    }
}
