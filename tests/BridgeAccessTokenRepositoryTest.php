<?php

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class BridgeAccessTokenRepositoryTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function test_access_tokens_can_be_persisted()
    {
        $expiration = Carbon::now();

        $tokenRepository = Mockery::mock('Lichv\Passport\TokenRepository');

        $events = Mockery::mock('Illuminate\Contracts\Events\Dispatcher');

        $tokenRepository->shouldReceive('create')->once()->andReturnUsing(function ($array) use ($expiration) {
            $this->assertEquals(1, $array['id']);
            $this->assertEquals(2, $array['user_id']);
            $this->assertEquals('client-id', $array['client_id']);
            $this->assertEquals(['scopes'], $array['scopes']);
            $this->assertEquals(false, $array['revoked']);
            $this->assertInstanceOf('DateTime', $array['created_at']);
            $this->assertInstanceOf('DateTime', $array['updated_at']);
            $this->assertEquals($expiration, $array['expires_at']);
        });

        $events->shouldReceive('dispatch')->once();

        $accessToken = new Lichv\Passport\Bridge\AccessToken(2, [new Lichv\Passport\Bridge\Scope('scopes')]);
        $accessToken->setIdentifier(1);
        $accessToken->setExpiryDateTime($expiration);
        $accessToken->setClient(new Lichv\Passport\Bridge\Client('client-id', 'name', 'redirect'));

        $repository = new Lichv\Passport\Bridge\AccessTokenRepository($tokenRepository, $events);

        $repository->persistNewAccessToken($accessToken);
    }
}
