<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Usage: ->middleware('role:Administrator,Admin Gudang')
     * Khusus: not:Kepala Gudang  -> middleware('role_not:Kepala Gudang')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }
        // dukung sintaks "not:X" via parameter terpisah? gunakan RoleNot middleware untuk itu.
        if (!in_array($user->hak_akses, $roles)) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
        }
        return $next($request);
    }
}
