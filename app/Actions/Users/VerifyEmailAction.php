<?php

namespace App\Actions\Users;

use App\Enums\Api\UserResponseCode;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;

class VerifyEmailAction
{
    use ApiResponse;

    public function handle(EmailVerificationRequest $request): JsonResponse
    {
        $alreadyVerified = $request->user()->hasVerifiedEmail();
        $request->fulfill();

        return $this->success($alreadyVerified ? UserResponseCode::EmailAlreadyVerified : UserResponseCode::EmailVerified, [
            'verified' => true,
            'email_verified_at' => $request->user()->email_verified_at,
        ]);
    }
}
