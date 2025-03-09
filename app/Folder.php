<?php

namespace App;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

readonly class Folder
{

    public function __construct(protected array $data)
    {
    }

    public static function root(): Folder
    {
        return new RootFolder();
    }

    public static function all(bool $reload = false): Collection
    {
        $cached = Cache::get('folders');

        if (!$reload && isset($cached)) {
            return $cached;
        }

        $folders = (new Client())
            ->folders()
            ->map(fn($folder) => new Folder((array)$folder));

        $folders->prepend(self::root());

        Cache::put('folders', $folders);

        return $folders;
    }

    public static function serialize(array $folder, int $index, Collection $folders): array
    {
        return [$index, 'label' => $folder['label'], 'parent' => @$folders->first(fn(array $f) => $f['id'] === $folder['parent_id'])['label'] ?? '---'];
    }

    public static function filter(array $folder, int $index, Collection $folders, string $search): bool
    {
        return str_contains(strtolower(join(', ', Folder::serialize($folder, $index, $folders))), $search);
    }

    public static function find(string $id): ?self
    {
        return self::all()->first(fn(Folder $folder) => $id === $folder->data['id']);
    }

    public function decrypt(): array
    {
        $decrypted = Crypto::init()
            ->decrypt($this->data, ['label']);

        return [
            'id' => $this->data['id'],
            'parent_id' => $this->data['parent'],
            ...$decrypted,
        ];
    }
}
