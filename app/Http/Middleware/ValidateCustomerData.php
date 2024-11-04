<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\ActivityLog;

class ValidateCustomerData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $rules = [
            'dni' => 'required|string|max:45',
            'id_reg' => 'required|exists:regions,id',
            'id_com' => 'required|exists:communes,id',
            'email' => 'required|string|max:120|email',
            'name' => 'required|string|max:45',
            'last_name' => 'required|string|max:45',
            'address' => 'string|max:255|nullable',
            'date_reg' => 'required|date',
            'status' => 'required|in:A,I,trash'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            $action = $request->isMethod('post') ? 'register_customer_failed' : 'update_customer_failed';
            $details = $request->isMethod('post') ? 'Intento de registro fallido: ' : 'Intento de actualización fallido: ';

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'details' => $details . $errors,
                'ip_address' => $request->ip()
            ]);
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->all()
            ], 400);
        }

        return $next($request);
    }
}
