<?php

namespace App\Http\Middleware;

use App\Master;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaster
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Master::empty()) {
            return response()->view('login');
        }

        return $next($request);
    }
}
