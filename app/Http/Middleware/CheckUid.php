<?php

namespace App\Http\Middleware;

use Closure;

class CheckUid
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
        if(empty($_COOKIE['uid'])){
            header('Refresh:2;url=/user/login');
            echo 'No UID ，请先登录';echo '</br>';
        }
        return $next($request);
    }
}
