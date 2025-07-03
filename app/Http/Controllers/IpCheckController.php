<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IpCheckController extends Controller
{
    protected function ipInRange($ip, $start, $end)
    {
        $ip = ip2long($ip);
        $start = ip2long($start);
        $end = ip2long($end);
        return ($ip >= $start && $ip <= $end);
    }

    public function check(Request $request)
    {
        $userIp = $request->ip();

        $allowedStart = '10.10.0.0';
        $allowedEnd   = '10.10.7.255';

        if ($this->ipInRange($userIp, $allowedStart, $allowedEnd)) {
            return response()->json(['result' => 'allowed', 'ip' => $userIp]);
        } else {
            return response()->json(['result' => 'blocked', 'ip' => $userIp], 403);
        }
    }
}
