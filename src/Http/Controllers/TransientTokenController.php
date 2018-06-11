<?php

namespace Lichv\Passport\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lichv\Passport\ApiTokenCookieFactory;

class TransientTokenController
{
    /**
     * The cookie factory instance.
     *
     * @var \Lichv\Passport\ApiTokenCookieFactory
     */
    protected $cookieFactory;

    /**
     * Create a new controller instance.
     *
     * @param  \Lichv\Passport\ApiTokenCookieFactory  $cookieFactory
     * @return void
     */
    public function __construct(ApiTokenCookieFactory $cookieFactory)
    {
        $this->cookieFactory = $cookieFactory;
    }

    /**
     * Get a fresh transient token cookie for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function refresh(Request $request)
    {
        return (new Response('Refreshed.'))->withCookie($this->cookieFactory->make(
            $request->user()->getKey(), $request->session()->token()
        ));
    }
}
