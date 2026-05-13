<?php

namespace App\Services;

use Illuminate\Support\Carbon;

class JwtService
{
    public function issueToken(array $claims): string
    {
        $now = Carbon::now()->timestamp;
        $ttlMinutes = (int) env('APP_JWT_TTL_MINUTES', 10080);
        $exp = Carbon::now()->addMinutes(max(1, $ttlMinutes))->timestamp;

        $payload = array_merge($claims, [
            'iat' => $now,
            'exp' => $exp,
        ]);

        return $this->encode($payload);
    }

    public function decodeToken(string $jwt): ?array
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return null;
        }

        [$headerPart, $payloadPart, $signaturePart] = $parts;
        $expected = $this->base64UrlEncode(hash_hmac('sha256', $headerPart.'.'.$payloadPart, $this->signingSecret(), true));
        if (!hash_equals($expected, $signaturePart)) {
            return null;
        }

        $payloadRaw = $this->base64UrlDecode($payloadPart);
        if ($payloadRaw === null) {
            return null;
        }

        $payload = json_decode($payloadRaw, true);
        if (!is_array($payload)) {
            return null;
        }

        $now = Carbon::now()->timestamp;
        if (isset($payload['exp']) && is_numeric($payload['exp']) && $now > (int) $payload['exp']) {
            return null;
        }

        return $payload;
    }

    private function encode(array $payload): string
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $headerPart = $this->base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES) ?: '{}');
        $payloadPart = $this->base64UrlEncode(json_encode($payload, JSON_UNESCAPED_SLASHES) ?: '{}');
        $signature = hash_hmac('sha256', $headerPart.'.'.$payloadPart, $this->signingSecret(), true);
        $signaturePart = $this->base64UrlEncode($signature);

        return $headerPart.'.'.$payloadPart.'.'.$signaturePart;
    }

    private function signingSecret(): string
    {
        $explicit = (string) env('APP_JWT_SECRET', '');
        if ($explicit !== '') {
            return $explicit;
        }

        return (string) env('APP_KEY', 'CAMBIAR_EN_PRODUCCION');
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $value): ?string
    {
        $normalized = strtr($value, '-_', '+/');
        $padLength = (4 - (strlen($normalized) % 4)) % 4;
        $normalized .= str_repeat('=', $padLength);
        $decoded = base64_decode($normalized, true);

        return $decoded === false ? null : $decoded;
    }
}
