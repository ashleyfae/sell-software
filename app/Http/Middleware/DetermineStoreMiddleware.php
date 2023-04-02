<?php

namespace App\Http\Middleware;

use App\Actions\Stores\StoreDeterminer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Determines:
 *
 * 1. The current user's stores.
 * 2. Which store the current user has selected.
 */
class DetermineStoreMiddleware
{
    public function __construct(protected StoreDeterminer $storeDeterminer)
    {

    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure(Request): (Response)  $next
     *
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->storeDeterminer->determineForRequest($request);

        $request->merge(['currentStore' => $this->storeDeterminer->currentStore]);

        return $next($request);
    }
}
