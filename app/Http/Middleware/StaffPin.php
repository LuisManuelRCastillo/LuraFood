<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StaffPin
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('staff_authenticated')) {
            return redirect()->route('staff.pin')->with('intended', $request->url());
        }

        return $next($request);
    }
}
