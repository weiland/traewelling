<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PermissionsPolicyMiddleware
{
    /**
     * Add permission policies to an request.
     *
     * @param  \Illuminate\Http\Request                                        $request
     * @param  \Closure(\Illuminate\Http\Request): (Response|RedirectResponse) $next
     *
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse {
        $response = $next($request);

        $response->header("Permissions-Policy", "web-share=(self)");

        return $response;
    }
}
