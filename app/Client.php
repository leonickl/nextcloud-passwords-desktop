<?php

namespace App;

use App\Exceptions\UnauthorizedException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

readonly class Client
{
    private Env $env;

    public function __construct()
    {
        $this->env = new Env;
    }

    public static function login(): void
    {
        (new Client)->openSession();
    }

    private function requestSession(): string
    {
        $response = Http::withBasicAuth($this->env->user, $this->env->password)
            ->get($this->endpoint('session.request'));

        Cache::put('xapitoken', $response->header('X-API-SESSION'));

        return Crypto::solveChallenge(
            password: Master::ask(),
            salts: $response->object()->challenge->salts,
        );
    }

    public static function logout(): void
    {
        Cache::forget('master');
    }

    public function passwords(): Collection
    {
        $response = Http::withBasicAuth($this->env->user, $this->env->password)
            ->withHeaders([
                'x-api-session' => $this->getXAPIToken(),
            ])
            ->get($this->endpoint('password.list'))
            ->object();

        if (@$response->status === "error") {
            throw new UnauthorizedException($response->message);
        }

        if (is_array($response)) {
            return collect($response);
        }

        if ($response instanceof Collection) {
            return $response;
        }

        dd($response);
    }

    public function folders(): Collection
    {
        $response = Http::withBasicAuth($this->env->user, $this->env->password)
            ->withHeaders([
                'x-api-session' => $this->getXAPIToken(),
            ])
            ->get($this->endpoint('folder.list'))
            ->object();

        if (@$response->status === "error") {
            throw new UnauthorizedException($response->message);
        }

        if (is_array($response)) {
            return collect($response);
        }

        if ($response instanceof Collection) {
            return $response;
        }

        dd($response);
    }

    public function openSession(): object
    {
        $challenge = $this->requestSession();

        $body = Http::withBasicAuth($this->env->user, $this->env->password)
            ->withHeaders([
                'x-api-session' => $this->getXAPIToken(),
            ])
            ->post($this->endpoint('session.open'), ['challenge' => $challenge])
            ->object();

        if (@$body->status === "error") {
            throw new UnauthorizedException($body->message);
        }

        if (@$body->success !== true) {
            dd($body);
        }

        $keys = $body->keys;

        Cache::put('keys', $keys);

        return $keys;
    }

    private function endpoints(): array
    {
        return [
            'tag.list' => 'api/1.0/tag/list',
            'tag.find' => 'api/1.0/tag/find',
            'tag.show' => 'api/1.0/tag/show',
            'tag.create' => 'api/1.0/tag/create',
            'tag.update' => 'api/1.0/tag/update',
            'tag.delete' => 'api/1.0/tag/delete',
            'tag.restore' => 'api/1.0/tag/restore',
            'share.list' => 'api/1.0/share/list',
            'share.find' => 'api/1.0/share/find',
            'share.show' => 'api/1.0/share/show',
            'share.create' => 'api/1.0/share/create',
            'share.update' => 'api/1.0/share/update',
            'share.delete' => 'api/1.0/share/delete',
            'share.partners' => 'api/1.0/share/partners',
            'client.list' => 'api/1.0/client/list',
            'client.show' => 'api/1.0/client/show',
            'client.create' => 'api/1.0/client/create',
            'client.update' => 'api/1.0/client/update',
            'client.delete' => 'api/1.0/client/delete',
            'folder.list' => 'api/1.0/folder/list',
            'folder.find' => 'api/1.0/folder/find',
            'folder.show' => 'api/1.0/folder/show',
            'folder.create' => 'api/1.0/folder/create',
            'folder.update' => 'api/1.0/folder/update',
            'folder.delete' => 'api/1.0/folder/delete',
            'folder.restore' => 'api/1.0/folder/restore',
            'password.list' => 'api/1.0/password/list',
            'password.find' => 'api/1.0/password/find',
            'password.show' => 'api/1.0/password/show',
            'password.create' => 'api/1.0/password/create',
            'password.update' => 'api/1.0/password/update',
            'password.delete' => 'api/1.0/password/delete',
            'password.restore' => 'api/1.0/password/restore',
            'password.generate' => 'api/1.0/service/password',
            'settings.get' => 'api/1.0/settings/get',
            'settings.set' => 'api/1.0/settings/set',
            'settings.list' => 'api/1.0/settings/list',
            'settings.reset' => 'api/1.0/settings/reset',
            'token.request' => 'api/1.0/token/{provider}/request',
            'session.request' => 'api/1.0/session/request',
            'session.open' => 'api/1.0/session/open',
            'session.keepalive' => 'api/1.0/session/keepalive',
            'session.close' => 'api/1.0/session/close',
            'keychain.get' => 'api/1.0/keychain/get',
            'keychain.set' => 'api/1.0/keychain/set',
            'challenge.get' => 'api/1.0/account/challenge/get',
            'challenge.set' => 'api/1.0/account/challenge/set',
            'account.reset' => 'api/1.0/account/reset',
            'service.coffee' => 'api/1.0/service/coffee',
            'service.avatar' => 'api/1.0/service/avatar/{user}/{size}',
            'service.favicon' => 'api/1.0/service/favicon/{domain}/{size}',
            'service.preview' => 'api/1.0/service/preview/{domain}/{view}/{width}/{height}',
            'cron.sharing' => 'cron/sharing',
            'link.request' => 'link/connect/request',
            'link.await' => 'link/connect/await',
            'link.reject' => 'link/connect/reject',
            'link.confirm' => 'link/connect/confirm',
        ];
    }

    public function endpoint(string $key): string
    {
        return $this->env->url . 'index.php/apps/passwords/' . $this->endpoints()[$key];
    }

    private function getXAPIToken(): string|null
    {
        return Cache::get('xapitoken');
    }
}
