<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MandatorySessionKeys
{
    /**
     * Check to see if the required session variables are set
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has(['job_items', 'job_repository'])) {
            abort(403, trans('messages.sessionmissing'));
        }

        return $next($request);
    }
}
