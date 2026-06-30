<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Проверяем, является ли пользователь администратором
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Доступ запрещен');
        }

        return $next($request);
    }
}
