<?php

namespace App\Actions\Users;

use App\Enums\Api\UserResponseCode;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SendEmailVerificationAction
{
    use ApiResponse;

    public function handle(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->success(UserResponseCode::EmailAlreadyVerified, ['verified' => true]);
        }
        $request->user()->sendEmailVerificationNotification();

        return $this->success(UserResponseCode::VerificationLinkSent, ['verified' => false]);
    }
}
