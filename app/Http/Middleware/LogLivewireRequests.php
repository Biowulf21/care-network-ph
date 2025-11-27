<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogLivewireRequests
{
    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();
        if (str_starts_with($path, 'livewire/message')) {
            try {
                $payload = $request->all();
                Log::channel('single')->info('Livewire request payload', ['path' => $path, 'payload' => $payload]);
            } catch (\Throwable $e) {
                Log::channel('single')->error('Failed logging Livewire payload: ' . $e->getMessage());
            }
        }

        return $next($request);
    }
}
