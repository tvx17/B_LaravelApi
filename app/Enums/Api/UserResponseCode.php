<?php

namespace App\Enums\Api;

enum UserResponseCode: string
{
    case LoginSuccessful = '05001';
    case CredentialsError = '05002';
    case LogoutSuccessful = '05003';
    case CurrentUser = '05004';
}
