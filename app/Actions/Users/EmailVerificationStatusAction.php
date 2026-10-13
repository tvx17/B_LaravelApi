<?php

namespace App\Actions\Users;

use App\Enums\Api\UserResponseCode;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerificationStatusAction
{
    use ApiResponse;

    public function handle(Request $request): JsonResponse
    {
        return $this->success(UserResponseCode::EmailVerificationStatus, [
            'verified' => $request->user()->hasVerifiedEmail(),
            'email_verified_at' => $request->user()->email_verified_at,
        ]);
    }
}
