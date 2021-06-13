<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class LogActivity
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
        if(Auth::user()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'ip_address'=> $request->ip(),
                'user_agent'=> $request->userAgent(),
                'route'=> request()->fullUrl()
            ]);
        }

        return $response;
    }
}
