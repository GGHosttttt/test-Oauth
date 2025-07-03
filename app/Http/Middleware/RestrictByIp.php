<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Response;

class RestrictByIp
{
    /**
     * List of allowed IP addresses or ranges based on your subnet.
     *
     * @var array
     */
    protected $allowedIps = [
        '10.10.0.0/21', // Your calculated subnet range
        // '127.0.0.1',    // Allow localhost for local development
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Use manual test IP from query parameter or header for local testing
        $testIp = $request->query('test_ip', $request->header('X-Test-IP'));

        // Use test IP if provided, otherwise fall back to request IP
        $clientIp = $testIp ?: $request->ip();
        Log::info('Client IP (Test/Real): ' . $clientIp); // Debug log

        if (!IpUtils::checkIp($clientIp, $this->allowedIps)) {
            Log::warning('Unauthorized IP attempt: ' . $clientIp);
            return response()->json([
                'error' => 'Unauthorized IP address',
                'ip' => $clientIp,
                'test_mode' => $testIp ? 'enabled' : 'disabled',
            ], 403);
        }

        return $next($request);
    }
}
