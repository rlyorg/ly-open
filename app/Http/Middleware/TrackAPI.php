<?php

namespace App\Http\Middleware;

use App\Jobs\GampQueue;
use Closure;
use Illuminate\Support\Str;
use Irazasyed\LaravelGAMP\Facades\GAMP;

class TrackAPI
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // dispatchAfterResponse dispatchSync
        // $clientId, $category, $action, $label
        GampQueue::dispatchAfterResponse($request->ip(), $request->path(), 'page='.$request->query('page', 1), 'api');
        return $next($request);

        
        // // Create a new UUID which is used as the Client ID
        // $uuid = (string) Str::uuid();

        // $gamp = GAMP::setClientId($uuid);
        // $gamp->setDocumentPath('/' . $request->path());
        // $gamp->setDocumentReferrer($request->server('HTTP_REFERER', ''));
        // $gamp->setUserAgentOverride($request->server('HTTP_USER_AGENT'));

        // // Override the sent IP with the IP from the current request.
        // // Otherwhise the servers IP would be sent.
        // $gamp->setIpOverride($request->ip());
        // $gamp->sendPageview();
    }
}