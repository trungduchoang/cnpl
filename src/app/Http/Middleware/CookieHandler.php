<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CookieHandler
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $cookie = '';
        if ($request->cookie('TAPCM')) {
            $cookie = $request->cookie('TAPCM');
        } else {
            $cookie = sha1("L'Alpe D'Huez".time().mt_rand().$_SERVER['REMOTE_ADDR'].$_SERVER['REMOTE_PORT'].$_SERVER['REQUEST_URI']);
        }
        $response = $next($request);
        if (get_class($response) === 'Illuminate\Http\JsonResponse') {
            if (property_exists($response->getData(), 'cookie')) {
                if ($response->getData()->cookie) {
                    setcookie('TAPCM', $response->getData()->cookie, 0x7f000000, '/');
                    setcookie('PLATEID_TAPCM', $response->getData()->cookie, 0x7f000000, '/', 'plate.id');
                }
            }
        } else {
            ;
        }

        return $response;
    }
}
