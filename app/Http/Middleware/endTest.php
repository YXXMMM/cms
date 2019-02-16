<?php

namespace App\Http\Middleware;

use Closure;

class endTest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        $response = $next($request);
        echo '</br>';
        echo 'Bye Bye';
        return $response;
    }

}
