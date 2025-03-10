<?php

use App\Client;
use App\Crypto;
use App\Folder;
use App\Password;
use App\Runtime;
use Illuminate\Support\Facades\Artisan;

Artisan::command('pwd:login', function () {
    Client::login();
});

Artisan::command('pwd:logout', function () {
    Client::logout();
});

Artisan::command('pwd:passwords {--reload} {search?}', function (bool $reload, ?string $search = null) {
    Runtime::run(function (bool $reload, ?string $search = null) {
        $passwords = Password::all($reload);

        if (count($passwords) === 0) {
            $this->warn('No passwords found.');
            return;
        }

        $fields = ['label', 'username', 'url', 'notes'];

        $passwords = $passwords->map->decrypt($fields);

        if ($search) {
            $search = strtolower($search);
            $filtered = $passwords->filter(fn(array $password, int $index) => Password::filter($password, $index, $search));
        } else {
            $filtered = $passwords;
        }

        if (count($filtered) === 1) {
            Password::find($filtered->first()['id'])->details();
        } else {
            $this->table(['index', ...$fields], $filtered->map(fn(array $password, int $index) => Password::serialize($password, $index)));
        }
    }, $this, [$reload, $search]);
});

Artisan::command('pwd:folders {--reload} {search?}', function (bool $reload, ?string $search = null) {
    Runtime::run(function (bool $reload, ?string $search = null) {
        $folders = Folder::all($reload);

        if (count($folders) === 0) {
            $this->warn('No folders found.');
            return;
        }

        $folders = $folders->map->decrypt();

        if ($search) {
            $search = strtolower($search);
            $filtered = $folders->filter(fn(array $folder, int $index) => Folder::filter($folder, $index, $folders, $search));
        } else {
            $filtered = $folders;
        }

        $this->table(['index', 'parent_id', 'label'], $filtered->map(fn(array $folder, int $index) => Folder::serialize($folder, $index, $folders)));
    }, $this, [$reload, $search]);
});

Artisan::command('pwd:folder {index}', function (int $index) {
    Runtime::run(function (int $index) {
        $folder = Folder::all()[$index];

        if (!$folder) {
            $this->warn('Folder not found.');
            return;
        }

        $folder = $folder->decrypt();

        $this->info($folder['label']);
        $this->newLine();

        $passwords = Password::all()->filter(fn(Password $password) => $password->folder() === $folder['id']);

        $fields = ['label', 'username', 'url', 'notes'];

        $passwords = $passwords->map->decrypt($fields);

        $this->table(['index', ...$fields], $passwords->map(fn(array $password, int $index) => Password::serialize($password, $index)));
    }, $this, [$index]);
});

Artisan::command('pwd:password {index}', function (int $index) {
    Runtime::run(function (int $index) {
        $password = Password::all()[$index];

        if (!$password) {
            $this->warn('Password not found.');
            return;
        }

        $password->details();
    }, $this, [$index]);
});

Artisan::command('pwd:sync', function () {
    Runtime::run(function () {
        Client::sync();
    }, $this, []);
});
