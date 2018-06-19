<?php

namespace Lichv\Passport\Bridge;

use Lichv\OAuth2\Server\Entities\Traits\EntityTrait;
use Lichv\OAuth2\Server\Entities\Traits\RefreshTokenTrait;
use Lichv\OAuth2\Server\Entities\RefreshTokenEntityInterface;

class RefreshToken implements RefreshTokenEntityInterface
{
    use EntityTrait, RefreshTokenTrait;
}
