<?php

namespace App\Support\Documentation;

use App\Enums\Api\SystemResponseCode;
use App\Enums\Api\UserResponseCode;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\UserController;
use Dedoc\Scramble\Contracts\OperationTransformer;
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\Generator\Parameter;
use Dedoc\Scramble\Support\Generator\RequestBodyObject;
use Dedoc\Scramble\Support\Generator\Response;
use Dedoc\Scramble\Support\Generator\Schema;
use Dedoc\Scramble\Support\Generator\Types\ArrayType;
use Dedoc\Scramble\Support\Generator\Types\BooleanType;
use Dedoc\Scramble\Support\Generator\Types\IntegerType;
use Dedoc\Scramble\Support\Generator\Types\MixedType;
use Dedoc\Scramble\Support\Generator\Types\NullType;
use Dedoc\Scramble\Support\Generator\Types\ObjectType;
use Dedoc\Scramble\Support\Generator\Types\StringType;
use Dedoc\Scramble\Support\Generator\Types\Type;
use Dedoc\Scramble\Support\RouteInfo;

/** Documents the HTTP contract that action delegation and exception handling hide from inference. */
class ApiOperationTransformer implements OperationTransformer
{
    public function handle(Operation $operation, RouteInfo $routeInfo): void
    {
        $authenticated = collect($routeInfo->route->gatherMiddleware())
            ->contains(fn (string $middleware): bool => $middleware === 'auth' || str_starts_with($middleware, 'auth:'));
        if (! $authenticated) {
            $operation->security = [];
        }
        if (! in_array($routeInfo->className(), [SystemController::class, UserController::class], true)) {
            return;
        }

        $method = $routeInfo->route->getActionMethod();
        $codes = match ($method) {
            'index', 'status' => [SystemResponseCode::Success->value],
            'login' => [UserResponseCode::LoginSuccessful->value],
            'logout' => [UserResponseCode::LogoutSuccessful->value],
            'me' => [UserResponseCode::CurrentUser->value],
            'emailVerificationStatus' => [UserResponseCode::EmailVerificationStatus->value],
            'sendEmailVerification' => [UserResponseCode::VerificationLinkSent->value, UserResponseCode::EmailAlreadyVerified->value],
            'verifyEmail' => [UserResponseCode::EmailVerified->value, UserResponseCode::EmailAlreadyVerified->value],
            default => [],
        };
        if ($codes === []) {
            return;
        }

        $data = new MixedType;
        foreach ($operation->responses ?? [] as $response) {
            $schema = $response instanceof Response && $response->code === 200
                ? ($response->content['application/json'] ?? null) : null;
            if ($schema instanceof Schema && $schema->type instanceof ObjectType) {
                $data = $schema->type->properties['data'] ?? $data;
            }
        }
        if ($method === 'login') {
            $data = $this->user();
            $operation->addRequestBodyObject(RequestBodyObject::make()->required()->setContent('application/json', Schema::fromType(
                (new ObjectType)->addProperty('email', (new StringType)->format('email')->setMax(255))
                    ->addProperty('password', (new StringType)->format('password')->setMax(255))
                    ->addProperty('remember', new BooleanType)->setRequired(['email', 'password']),
            )));
        } elseif ($method === 'logout') {
            $data = new NullType;
        }

        $operation->responses = [$this->response(200, $codes, $data, 'Erfolgreiche Antwort.')];
        $errors = [500 => SystemResponseCode::ServerError];
        if (in_array('auth:sanctum', $routeInfo->route->gatherMiddleware(), true)) {
            $errors[401] = SystemResponseCode::Unauthenticated;
        }
        if ($method === 'login') {
            $errors[401] = UserResponseCode::CredentialsError;
            $errors[422] = SystemResponseCode::ValidationError;
        }
        if (in_array($method, ['login', 'logout', 'verifyEmail'], true)) {
            $errors[403] = SystemResponseCode::Forbidden;
        }
        if (in_array($method, ['login', 'sendEmailVerification', 'verifyEmail'], true)) {
            $errors[429] = SystemResponseCode::TooManyRequests;
        }
        if ($operation->method === 'post') {
            $errors[419] = SystemResponseCode::CsrfError;
            $header = new Parameter('X-XSRF-TOKEN', 'header');
            $header->required = true;
            $header->description = 'URL-dekodierter XSRF-TOKEN-Cookie nach GET /sanctum/csrf-cookie. Cookies mitsenden.';
            $header->schema = Schema::fromType(new StringType);
            $operation->parameters[] = $header;
        }
        if ($method === 'verifyEmail') {
            foreach (['expires', 'signature'] as $name) {
                $parameter = new Parameter($name, 'query');
                $parameter->required = true;
                $parameter->schema = Schema::fromType($name === 'expires' ? new IntegerType : new StringType);
                $parameter->description = 'Unverändert aus dem signierten Bestätigungslink übernehmen; Anmeldung desselben Kontos nötig.';
                $operation->parameters[] = $parameter;
            }
        }
        foreach ($errors as $httpStatus => $code) {
            $errorData = (new ObjectType)->addProperty('message', new StringType)->setRequired(['message']);
            if ($httpStatus === 422) {
                $errorData->addProperty('errors', (new ObjectType)->additionalProperties((new ArrayType)->setItems(new StringType)));
            }
            $nullData = ($method === 'login' && $httpStatus === 401)
                || (in_array($method, ['login', 'logout'], true) && $httpStatus === 403);
            $operation->addResponse($this->response($httpStatus, [$code->value], $nullData ? new NullType : $errorData, \Symfony\Component\HttpFoundation\Response::$statusTexts[$httpStatus] ?? 'CSRF token mismatch.'));
        }
    }

    private function response(int $httpStatus, array $codes, Type $data, string $description): Response
    {
        return Response::make($httpStatus)->setDescription($description)->setContent('application/json', Schema::fromType(
            (new ObjectType)->addProperty('code', (new StringType)->pattern('^\\d{5}$')->enum($codes))
                ->addProperty('status', (new BooleanType)->enum([$httpStatus < 400]))
                ->addProperty('data', $data)
                ->addProperty('responseStatus', (new IntegerType)->const($httpStatus))
                ->setRequired(['code', 'status', 'data', 'responseStatus']),
        ));
    }

    private function user(): ObjectType
    {
        return (new ObjectType)->addProperty('id', new IntegerType)
            ->addProperty('name', new StringType)->addProperty('email', (new StringType)->format('email'))
            ->addProperty('email_verified_at', (new StringType)->format('date-time')->nullable(true))
            ->addProperty('created_at', (new StringType)->format('date-time')->nullable(true))
            ->addProperty('updated_at', (new StringType)->format('date-time')->nullable(true))
            ->setRequired(['id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at']);
    }
}
