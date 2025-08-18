<?php

namespace App\Exceptions;

use Exception;

class Handler extends Exception
{
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof TokenMismatchException) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sesi telah berakhir. Silakan login kembali.'], 419);
            }
            return response()->view('errors.419', [], 419);
        }

        // 404 - Not found
        if ($exception instanceof NotFoundHttpException) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Halaman tidak ditemukan.'], 404);
            }
            return response()->view('errors.404', [], 404);
        }

        // 403 - Unauthorized / Forbidden
        if ($exception instanceof AccessDeniedHttpException) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Anda tidak memiliki akses.'], 403);
            }
            return response()->view('errors.403', [], 403);
        }

        // Default handler
        return parent::render($request, $exception);
    }

}
