<?php

namespace Lichv\Passport\Bridge;

use Lichv\OAuth2\Server\Entities\Traits\EntityTrait;
use Lichv\OAuth2\Server\Entities\Traits\AuthCodeTrait;
use Lichv\OAuth2\Server\Entities\AuthCodeEntityInterface;
use Lichv\OAuth2\Server\Entities\Traits\TokenEntityTrait;

class AuthCode implements AuthCodeEntityInterface
{
    use AuthCodeTrait, EntityTrait, TokenEntityTrait;
}
