<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class RoleMiddleware {
 public function handle(Request $request, Closure $next, string $role) {
   abort_unless(auth()->check() && auth()->user()->role === $role, 403, 'Akses ditolak.');
   return $next($request);
 }
}
