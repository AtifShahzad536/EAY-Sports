<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $settingsPath = storage_path('app/settings.json');

        if (file_exists($settingsPath)) {
            $settings = json_decode(file_get_contents($settingsPath), true);
            $maintenanceEnabled = $settings['maintenance_mode'] ?? false;

            if ($maintenanceEnabled) {
                // Exclude admin panel and its api endpoints
                if (! $request->is('admin*') && ! $request->is('api/admin*')) {
                    if ($request->expectsJson() || $request->header('X-Inertia')) {
                        return response()->json(['message' => 'Service Unavailable (Maintenance Mode)'], 503);
                    }
                    abort(503);
                }
            }
        }

        return $next($request);
    }
}
