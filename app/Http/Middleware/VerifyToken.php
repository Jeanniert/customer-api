<?php

namespace App\Http\Middleware;

use App\Models\Tokens;
use Closure;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Symfony\Component\HttpFoundation\Response;

class VerifyToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');

        if (!$token) {
            ActivityLog::create([
                'user_id' => null,
                'action' => 'register_failed',
                'details' => 'Token no proporcionado',
                'ip_address' => $request->ip()
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Token no proporcionado'
            ], 401);
        }

        $token = str_replace('Bearer ', '', $token);
        $tokenRecord = Tokens::valid()->where('token', $token)->first();

        if (!$tokenRecord) {
            ActivityLog::create([
                'user_id' => null,
                'action' => 'register_failed',
                'details' => 'Token inválido o expirado',
                'ip_address' => $request->ip()
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Token inválido o expirado'
            ], 401);
        }

        return $next($request);
    }
}     
