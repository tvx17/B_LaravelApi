<?php

namespace App\Actions\Users;

use App\Enums\Api\UserResponseCode;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeAction
{
    use ApiResponse;

    public function handle(Request $request): JsonResponse
    {
        return $this->success(UserResponseCode::CurrentUser, $request->user());
    }
}
