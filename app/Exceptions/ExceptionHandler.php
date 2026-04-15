<?php

namespace App\Exceptions;

use ArRahmouni\ResponseHelper\RequestAdminArea;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

class ExceptionHandler
{
    public function handleException(Exception|Throwable $e, Request $request)
    {
        $webContext = RequestAdminArea::isAdminControlPanel($request) ? 'admin' : 'front';

        switch (true) {
            case $e instanceof ModelNotFoundException:
            case $e instanceof NotFoundHttpException:
                return sendNotFoundResponse('record_not_found', $webContext);

            case $e instanceof MethodNotAllowedHttpException:
                $message = debugEnabled() ? $e->getMessage() : trans('response::messages.web_response_messages.method_not_allowed');

                return sendMethodNotAllowedResponse($message, $webContext);

            case $e instanceof AccessDeniedHttpException:
                return sendDontHavePermissionResponse('dont_have_permission', $webContext);

            case $e instanceof AuthenticationException:
                return sendUnauthorizedResponse();

            case $e instanceof ValidationException:
                return sendValidationResponse($e->validator);

            case $e instanceof TooManyRequestsHttpException:
                return sendTooManyRequestsResponse('too_many_requests', $webContext);
            default:
                report($e);

                if (! debugEnabled()) {
                    return sendServerErrorResponse(null, $webContext);
                }
        }
    }
}
