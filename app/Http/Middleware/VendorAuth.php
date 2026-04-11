<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VendorAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('vendor')) {
            return redirect()->route('vendor.login')->withErrors(['auth' => 'Silahkan login sebagai vendor.']);
        }

        return $next($request);
    }
}
