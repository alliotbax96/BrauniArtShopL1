<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ApiKeyAuth;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'api.key' => ApiKeyAuth::class,
        ]);

        // Исключаем CSRF для webhook маршрутов
        $middleware->validateCsrfTokens(except: [
            'payment/webhook',
            '/payment/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Для API запросов всегда возвращаем JSON
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthenticated',
                ], 401);
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Route not found',
                ], 404);
            }
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Method not allowed',
                    'allowed_methods' => $e->getHeaders()['Allow'] ?? [],
                ], 405);
            }
        });

        $exceptions->render(function (RouteNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Route not found',
                ], 404);
            }
        });

        $exceptions->render(function (InvalidSignatureException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid signature',
                ], 403);
            }
        });
    })->create();
