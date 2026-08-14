<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiKeyAuth
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->bearerToken();

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'error' => 'API key required',
            ], 401);
        }

        $keyRecord = ApiKey::where('key', $apiKey)->first();

        if (!$keyRecord) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid API key',
            ], 401);
        }

        if (!$keyRecord->isValid()) {
            return response()->json([
                'success' => false,
                'error' => 'API key expired or inactive',
            ], 401);
        }

        // Передаём ID ключа в запрос для дальнейшего использования
        $request->merge([
            'api_key_id' => $keyRecord->id,
            'api_key_name' => $keyRecord->name,
        ]);

        Log::info('API key authenticated', [
            'key_id' => $keyRecord->id,
            'name' => $keyRecord->name,
            'ip' => $request->ip(),
            'path' => $request->path(),
        ]);

        return $next($request);
    }
}
