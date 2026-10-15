<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Mengecek apakah role user yang sedang login bukan 'admin'
        if ($request->user()->role !== 'admin') {
            // Jika bukan admin, hentikan akses dan tampilkan error 403
            abort(403, 'Akses Ditolak! Halaman ini khusus Admin Satker');
        }

        // Jika dia admin, silahkan lanjut ke halaman yang dituju
        return $next($request);
    }
}
