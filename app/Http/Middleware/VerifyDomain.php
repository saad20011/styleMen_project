<?php

namespace App\Http\Middleware;

use Closure;

class VerifyDomain
{
    public function handle($request, Closure $next)
    {
        if (!in_array($request->getHttpHost(), ['loca.ma', 'localhost:8000' , 'localhost:3000'])) {
            // Return a response with an error message if the domain is not valid
            return response('Invalid domain', 403);
        }
        return $next($request);
    }
}
