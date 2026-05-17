<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use App\Services\JwtService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateCustomerJwt
{
    public function __construct(private readonly JwtService $jwtService)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if (!$token) {
            Log::channel('api')->warning('JWT missing for authenticated route', ['path' => $request->path()]);
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $claims = $this->jwtService->decodeToken($token);
        if (!$claims || !isset($claims['sub']) || !is_numeric($claims['sub'])) {
            Log::channel('api')->warning('JWT validation failed', ['token' => $token]);
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $customer = Customer::find((int) $claims['sub']);
        if (!$customer) {
            Log::channel('api')->warning('JWT customer not found', ['sub' => $claims['sub']]);
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $request->attributes->set('authCustomer', $customer);

        return $next($request);
    }
}
