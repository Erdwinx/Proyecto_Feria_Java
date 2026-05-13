<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use App\Services\JwtService;
use Closure;
use Illuminate\Http\Request;
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
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $claims = $this->jwtService->decodeToken($token);
        if (!$claims || !isset($claims['sub']) || !is_numeric($claims['sub'])) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $customer = Customer::find((int) $claims['sub']);
        if (!$customer) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $request->attributes->set('authCustomer', $customer);

        return $next($request);
    }
}
