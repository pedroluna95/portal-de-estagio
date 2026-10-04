<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontFlash([
            'senha',
            'confirmar_senha',
            'senha_atual',
            'nova_senha',
            'confirmar_nova_senha',
            'password',
            'password_confirmation',
            'current_password',
        ]);

        $exceptions->report(function (\Throwable $exception): bool {
            if (
                $exception instanceof ValidationException ||
                $exception instanceof AuthenticationException ||
                $exception instanceof AuthorizationException ||
                ($exception instanceof HttpExceptionInterface && $exception->getStatusCode() < 500)
            ) {
                return false;
            }

            $requestId = (string) str()->uuid();
            request()->attributes->set('error_request_id', $requestId);
            Log::error('Erro inesperado na aplicação', [
                'request_id' => $requestId,
                'exception' => $exception,
            ]);

            return false;
        });

        $exceptions->render(function (\Throwable $exception, \Illuminate\Http\Request $request) {
            if (
                $exception instanceof ValidationException ||
                $exception instanceof AuthenticationException ||
                $exception instanceof AuthorizationException ||
                ($exception instanceof HttpExceptionInterface && $exception->getStatusCode() < 500)
            ) {
                return null;
            }

            $requestId = $request->attributes->get('error_request_id') ?? (string) str()->uuid();
            $message = "Ocorreu uma falha inesperada ao processar sua solicitação. Tente novamente. Código de atendimento: {$requestId}";
            $status = $exception instanceof HttpExceptionInterface && $exception->getStatusCode() >= 500
                ? $exception->getStatusCode()
                : 500;

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'request_id' => $requestId,
                ], $status);
            }

            return response()->view('errors.500', [
                'requestId' => $requestId,
            ], $status);
        });
    })->create();
