<?php

namespace App\Actions\Users;

use App\Enums\Api\SystemResponseCode;
use App\Enums\Api\UserResponseCode;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Contracts\LoginResponse;

class LoginAction
{
    use ApiResponse;

    public function handle(Request $request): JsonResponse
    {
        if (! $request->hasSession()) {
            return $this->error(SystemResponseCode::Forbidden, null, 403);
        }

        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        try {
            return app(Pipeline::class)->send($request)
                ->through([AttemptToAuthenticate::class, PrepareAuthenticatedSession::class])
                ->then(fn (Request $request): JsonResponse => app(LoginResponse::class)->toResponse($request));
        } catch (ValidationException) {
            return $this->error(UserResponseCode::CredentialsError, null, 401);
        }
    }
}
