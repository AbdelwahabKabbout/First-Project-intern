<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\log;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $permissionId)
    {
        if (! $request->user() || ! $request->user()->permissions()->where('permissions.id', $permissionId)->exists()) {
            log::create([
                'action' => 'Access Denied',
                'details' => 'User has tried invalid access'
            ]);
            return redirect()->route('guestbook.index')->withErrors('You do not have permission to access this page.');
        }
        return $next($request);
    }
}
