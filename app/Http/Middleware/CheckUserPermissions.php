<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserPermissions
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect('login');
        }

        $routeName = $request->route()->getName();

        // Проверка доступа к кабинету продавца
        if (str_starts_with($routeName, 'seller.') && !$user->isSeller()) {
            abort(403, 'Доступ запрещён');
        }

        // Проверка доступа к админке
        if (str_starts_with($routeName, 'admin.') && !$user->isAdmin()) {
            abort(403, 'Доступ запрещён');
        }

        return $next($request);
    }
}
