<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\Visitor;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->ajax() && $request->isMethod('get')) {
            Visitor::firstOrCreate([
                'ip_address' => $request->ip(),
                'date' => today()->toDateString(),
            ]);
        }

        return $next($request);
    }
}
