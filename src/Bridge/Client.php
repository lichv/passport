<?php

namespace Lichv\Passport\Bridge;

use Lichv\OAuth2\Server\Entities\Traits\ClientTrait;
use Lichv\OAuth2\Server\Entities\Traits\EntityTrait;
use Lichv\OAuth2\Server\Entities\ClientEntityInterface;

class Client implements ClientEntityInterface
{
    use ClientTrait, EntityTrait;

    /**
     * Create a new client instance.
     *
     * @param  string  $identifier
     * @param  string  $name
     * @param  string  $redirectUri
     * @return void
     */
    public function __construct($identifier, $name, $redirectUri, $silence=0, $expire=0, $refresh_expire=0)
    {
        $this->setIdentifier((string) $identifier);

        $this->name = $name;
        $this->redirectUri = explode(',', $redirectUri);
        $this->silence = $silence;
        $this->expire = $expire;
        $this->refresh_expire = $refresh_expire;
    }
}
