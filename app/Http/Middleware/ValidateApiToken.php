<?php

namespace App\Http\Middleware;

use App\Models\ApiRequestLog;
use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $bearer = $request->bearerToken();

        if (! $bearer) {
            Log::warning('API auth failed: no token provided', ['ip' => $request->ip()]);
            ApiRequestLog::create([
                'ip'       => $request->ip(),
                'status'   => 'auth_failed',
                'detail'   => 'missing_token',
                'payload'  => $request->json()->all(),
                'response' => ['error' => 'Token required', 'http_status' => 401],
            ]);
            return response()->json(['error' => 'Token required'], 401);
        }

        $tokenHash = hash('sha256', $bearer);
        $apiToken = ApiToken::where('token_hash', $tokenHash)->first();

        if (! $apiToken) {
            Log::warning('API auth failed: token not found', ['ip' => $request->ip()]);
            ApiRequestLog::create([
                'ip'       => $request->ip(),
                'status'   => 'auth_failed',
                'detail'   => 'token_not_found',
                'payload'  => $request->json()->all(),
                'response' => ['error' => 'Invalid or expired token', 'http_status' => 401],
            ]);
            return response()->json(['error' => 'Invalid or expired token'], 401);
        }

        if (! $apiToken->isValid()) {
            $reason = $apiToken->revoked_at ? 'token_revoked' : 'token_expired';
            Log::warning('API auth failed: '.$reason, [
                'token_name' => $apiToken->name,
                'ip' => $request->ip(),
            ]);
            ApiRequestLog::create([
                'ip'       => $request->ip(),
                'status'   => 'auth_failed',
                'token_id' => $apiToken->id,
                'detail'   => $reason,
                'payload'  => $request->json()->all(),
                'response' => ['error' => 'Invalid or expired token', 'http_status' => 401],
            ]);
            return response()->json(['error' => 'Invalid or expired token'], 401);
        }

        $apiToken->update(['last_used_at' => now()]);

        $request->attributes->set('api_token', $apiToken);

        return $next($request);
    }
}
