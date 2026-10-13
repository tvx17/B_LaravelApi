<?php

namespace App\Http\Controllers;

use App\Actions\Users\EmailVerificationStatusAction;
use App\Actions\Users\LoginAction;
use App\Actions\Users\LogoutAction;
use App\Actions\Users\MeAction;
use App\Actions\Users\SendEmailVerificationAction;
use App\Actions\Users\VerifyEmailAction;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function login(Request $request, LoginAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function logout(Request $request, LogoutAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function me(Request $request, MeAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function emailVerificationStatus(Request $request, EmailVerificationStatusAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function sendEmailVerification(Request $request, SendEmailVerificationAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function verifyEmail(EmailVerificationRequest $request, VerifyEmailAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
