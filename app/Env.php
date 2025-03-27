<?php

namespace App;

readonly class Env
{
    public string $url;
    public string $user;
    public string $password;

    public function __construct()
    {
        $this->url = trim(env('NC_URL'), '/') . '/';
        $this->user = env('NC_USER');
        $this->password = env('NC_PASS');
    }
}
