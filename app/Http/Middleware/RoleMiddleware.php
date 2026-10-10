<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // सुरक्षा जांच: क्या यूजर लॉगइन है और उसका रोल लिस्ट में मैच हो रहा है?
        if (!$request->user() || !in_array($request->user()->role, $roles)) {
            return response()->json([
                'message' => 'Access Denied. Unauthorized role permission area.'
            ], 403);
        }

        return $next($request);
    }
}
