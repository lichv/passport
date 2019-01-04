<?php

namespace Lichv\Passport;

use RuntimeException;

class ClientRepository
{
    /**
     * Get a client by the given ID.
     *
     * @param  int  $id
     * @return \Lichv\Passport\Client|null
     */
    public function find($id)
    {
        $client = Passport::client();

        return $client->where($client->getKeyName(), $id)->first();
    }

    /**
     * Get an active client by the given ID.
     *
     * @param  int  $id
     * @return \Lichv\Passport\Client|null
     */
    public function findActive($id)
    {
        $client = $this->find($id);

        return $client && ! $client->revoked ? $client : null;
    }

    /**
     * Get an active client by the given key.
     *
     * @param  string  $key
     * @return \Lichv\Passport\Client|null
     */
    public function findKey($key){
        $client = Passport::client()->where('key', $key)->first();
        return $client && ! $client->revoked ? $client : null;
    }

    /**
     * Get a client instance for the given ID and user ID.
     *
     * @param  int  $clientId
     * @param  mixed  $userId
     * @return \Lichv\Passport\Client|null
     */
    public function findForUser($clientId, $userId)
    {
        $client = Passport::client();

        return $client
                    ->where($client->getKeyName(), $clientId)
                    ->where('user_id', $userId)
                    ->first();
    }

    /**
     * Get the client instances for the given user ID.
     *
     * @param  mixed  $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function forUser($userId)
    {
        return Passport::client()
                    ->where('user_id', $userId)
                    ->orderBy('name', 'asc')->get();
    }

    /**
     * Get the active client instances for the given user ID.
     *
     * @param  mixed  $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function activeForUser($userId)
    {
        return $this->forUser($userId)->reject(function ($client) {
            return $client->revoked;
        })->values();
    }

    /**
     * Get the personal access token client for the application.
     *
     * @return \Lichv\Passport\Client
     *
     * @throws \RuntimeException
     */
    public function personalAccessClient()
    {
        if (Passport::$personalAccessClientId) {
            return $this->find(Passport::$personalAccessClientId);
        }

        $client = Passport::personalAccessClient();

        if (! $client->exists()) {
            throw new RuntimeException('Personal access client not found. Please create one.');
        }

        return $client->orderBy($client->getKeyName(), 'desc')->first()->client;
    }

    /**
     * Store a new client.
     *
     * @param  int  $userId
     * @param  string  $name
     * @param  string  $redirect
     * @param  bool  $personalAccess
     * @param  bool  $password
     * @return \Lichv\Passport\Client
     */
    public function create($userId, $name, $redirect, $personalAccess = false, $password = false)
    {
        $client = Passport::client()->forceFill([
            'user_id' => $userId,
            'name' => $name,
            'key' => str_random(20),
            'secret' => str_random(40),
            'redirect' => $redirect,
            'personal_access_client' => $personalAccess,
            'password_client' => $password,
            'revoked' => false,
            'silence' => 0,
            'expire' => 0,
            'refresh_expire' => 0,
        ]);

        $client->save();

        return $client;
    }

    /**
     * Store a new personal access token client.
     *
     * @param  int  $userId
     * @param  string  $name
     * @param  string  $redirect
     * @return \Lichv\Passport\Client
     */
    public function createPersonalAccessClient($userId, $name, $redirect)
    {
        return tap($this->create($userId, $name, $redirect, true), function ($client) {
            $accessClient = Passport::personalAccessClient();
            $accessClient->client_id = $client->id;
            $accessClient->save();
        });
    }

    /**
     * Store a new password grant client.
     *
     * @param  int  $userId
     * @param  string  $name
     * @param  string  $redirect
     * @return \Lichv\Passport\Client
     */
    public function createPasswordGrantClient($userId, $name, $redirect)
    {
        return $this->create($userId, $name, $redirect, false, true);
    }

    /**
     * Update the given client.
     *
     * @param  Client  $client
     * @param  string  $name
     * @param  string  $redirect
     * @return \Lichv\Passport\Client
     */
    public function update(Client $client, $name, $redirect)
    {
        $client->forceFill([
            'name' => $name, 'redirect' => $redirect,
        ])->save();

        return $client;
    }

    /**
     * Regenerate the client secret.
     *
     * @param  \Lichv\Passport\Client  $client
     * @return \Lichv\Passport\Client
     */
    public function regenerateSecret(Client $client)
    {
        $client->forceFill([
            'secret' => str_random(40),
        ])->save();

        return $client;
    }

    /**
     * Determine if the given client is revoked.
     *
     * @param  int  $id
     * @return bool
     */
    public function revoked($id)
    {
        $client = $this->find($id);

        return is_null($client) || $client->revoked;
    }

    /**
     * Delete the given client.
     *
     * @param  \Lichv\Passport\Client  $client
     * @return void
     */
    public function delete(Client $client)
    {
        $client->tokens()->update(['revoked' => true]);

        $client->forceFill(['revoked' => true])->save();
    }
}
