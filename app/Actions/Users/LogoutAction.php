<?php

namespace App\Actions\Users;

use App\Enums\Api\SystemResponseCode;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LogoutResponse;

class LogoutAction
{
    use ApiResponse;

    public function handle(Request $request): JsonResponse
    {
        if (! $request->hasSession()) {
            return $this->error(SystemResponseCode::Forbidden, null, 403);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return app(LogoutResponse::class)->toResponse($request);
    }
}
