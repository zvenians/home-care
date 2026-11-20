<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(config('homecare.demo_mode'), 403, 'Akses tanpa autentikasi hanya untuk demo lokal.');
        abort_unless($request->session()->get('demo_role') === 'admin', 403, 'Beralih ke demo admin dahulu.');
        return $next($request);
    }
}
