<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class ExceptionHandler
{
    public function __invoke(Exception|Throwable $e)
    {
        return $this->handleException($e);
    }

    public function handleException(Exception|Throwable $e)
    {
        switch (true) {
            case $e instanceof ModelNotFoundException:
            case $e instanceof NotFoundHttpException:
                return sendNotFoundResponse();

            case $e instanceof MethodNotAllowedHttpException:
                $message = debugEnabled() ? $e->getMessage() : trans('response::messages.web_response_messages.method_not_allowed');
                return sendMethodNotAllowedResponse($message);

            case $e instanceof AccessDeniedHttpException:
                return sendDontHavePermissionResponse();

            case $e instanceof AuthenticationException:
                return sendUnauthorizedResponse();

            case $e instanceof ValidationException:
                return sendValidationResponse($e->validator);

            default:
                report($e);

                if(! debugEnabled()) return sendServerErrorResponse();

        }
    }
}

