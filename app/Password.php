<?php

namespace App;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

readonly class Password
{
    public function __construct(private array $data)
    {
    }

    public static function all(bool $reload = false): Collection
    {
        $cached = Cache::get('passwords');

        if (!$reload && isset($cached)) {
            return $cached;
        }

        $passwords = (new Client())
            ->passwords()
            ->map(fn($password) => new Password((array)$password));

        Cache::put('passwords', $passwords);

        return $passwords;
    }

    public static function find(string $id): ?Password
    {
        return self::all()->first(fn(Password $password) => $id === $password->data['id']);
    }

    public static function serialize(array $password, int $index): array
    {
        return [$index, ...array_map(fn(string $str) => substr(str_replace(["\r\n", "\n"], [" ", " "], $str), 0, 20), $password)];
    }

    public static function filter(array $password, int $index, string $search): bool
    {
        return str_contains(strtolower(join(', ', [$index, ...$password])), $search);
    }

    public function decrypt(?array $fields = null): array
    {
        $fields ??= ['label', 'username', 'notes', 'customFields', 'url'];

        $values = Crypto::init()
            ->decrypt($this->data, $fields);

        return ['id' => $this->data['id'], ...$values];
    }

    public function folder(): string
    {
        return $this->data['folder'];
    }

    public function details(): void
    {
        $fields = $this->decrypt(['label', 'username', 'url', 'notes', 'password']);

        foreach ($fields as $title => $field) {
            if (trim($field)) {
                Cli::get()->line($title . ': ' . $field);
            }
        }
    }
}
