<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Session;

class BlockUser
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
        if(Auth::check() && Auth::user()->status == 2){
            $attempt = $request->session()->get('count'.Auth::id());
            $request->session()->flash('failed', 'Your Account is suspended');
            if($attempt == 3){
                $request->session()->forget('count'.Auth::id());
                $request->session()->flash('failed', 'Your account was suspended because it was wrong in 3 times choosing the question, please contact the admin, thank you.');
            }
            Auth::logout();
            return redirect()->route('login');
        }
        return $response;
    }
}
