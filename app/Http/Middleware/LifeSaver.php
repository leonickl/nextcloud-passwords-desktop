<?php

namespace App\Http\Middleware;

use App\Master;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LifeSaver
{
    public function handle(Request $request, Closure $next): Response
    {
        Master::saveLife();
        return $next($request);
    }
}
