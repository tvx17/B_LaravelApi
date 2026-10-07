<?php

namespace App\Http\Controllers;

use App\Actions\Users\LoginAction;
use App\Actions\Users\LogoutAction;
use App\Actions\Users\MeAction;
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
}
