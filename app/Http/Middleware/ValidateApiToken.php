<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $bearer = $request->bearerToken();

        if (! $bearer) {
            return response()->json(['error' => 'Token required'], 401);
        }

        $tokenHash = hash('sha256', $bearer);
        $apiToken = ApiToken::where('token_hash', $tokenHash)->first();

        if (! $apiToken || ! $apiToken->isValid()) {
            return response()->json(['error' => 'Invalid or expired token'], 401);
        }

        $apiToken->update(['last_used_at' => now()]);

        $request->attributes->set('api_token', $apiToken);

        return $next($request);
    }
}
