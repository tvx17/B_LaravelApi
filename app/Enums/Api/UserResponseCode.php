<?php

namespace App\Enums\Api;

enum UserResponseCode: string
{
    case LoginSuccessful = '05001';
    case CredentialsError = '05002';
    case LogoutSuccessful = '05003';
    case CurrentUser = '05004';
    case EmailVerificationStatus = '05005';
    case VerificationLinkSent = '05006';
    case EmailVerified = '05007';
    case EmailAlreadyVerified = '05008';
}
