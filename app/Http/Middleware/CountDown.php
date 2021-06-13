<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\AvCoin;

class CountDown
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
        $ico = AvCoin::ico();
        if($ico){
            return redirect()->route('count_down');
        }
        return $response;
    }
}
