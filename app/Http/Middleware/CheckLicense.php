<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLicense
{
    public function handle(Request $request, Closure $next): Response
    {
        $expiredAt = \Carbon\Carbon::parse(
            base64_decode(config('license.expired_at'))
        );

        if (now()->greaterThan($expiredAt)) {
            return response()->view('errors.license-expired', [], 403);
        }

        return $next($request);
    }
}
