<?php

namespace App;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

readonly class Folder
{

    private array $color;

    public function __construct(protected array $data)
    {
        $this->color = collect([
            ['bg-gray-600', 'text-gray-600'],   // Dark Gray
            ['bg-blue-700', 'text-blue-700'],   // Muted Navy
            ['bg-green-700', 'text-green-700'],  // Deep Green
            ['bg-indigo-700', 'text-indigo-700'], // Dark Indigo
            ['bg-yellow-700', 'text-yellow-700'], // Mustard Yellow
            ['bg-red-700', 'text-red-700'],    // Deep Red
            ['bg-teal-700', 'text-teal-700'],   // Muted Teal
            ['bg-purple-700', 'text-purple-700'], // Dark Purple
            ['bg-amber-700', 'text-amber-700'],  // Warm Amber
            ['bg-rose-700', 'text-rose-700'],    // Soft Rose
        ])->random();
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
        $parent = @$folders->first(fn(array $f) => $f['id'] === $folder['parent_id'])['label'] ?? '---';

        return [
            'index' => $index,
            'label' => $folder['label'],
            'parent' => $parent,
        ];
    }

    public static function filter(array $folder, int $index, Collection $folders, string $search): bool
    {
        return str_contains(strtolower(join(', ', Folder::serialize($folder, $index, $folders))), $search);
    }

    public static function find(string $id): ?self
    {
        return self::all()->first(fn(Folder $folder) => $id === $folder->data['id']);
    }

    public function id(): string
    {
        return $this->data['id'];
    }

    public function parent(): ?string
    {
        return $this->data['parent'] ?? null;
    }

    public function decrypt(): array
    {
        $decrypted = Crypto::init()
            ->decrypt($this->data, ['label']);

        $parent = $this->parent() ? Folder::find($this->parent())->decrypt() : null;

        return [
            'id' => $this->data['id'],
            'parent_id' => $this->data['parent'],
            'color' => $this->color[0] ?? null,
            'color_fg' => $this->color[1] ?? null,
            'label' => $parent && $parent['id'] === '00000000-0000-0000-0000-000000000000'
                ? $decrypted['label']
                : $parent['label'] . '/' . $decrypted['label'],
            'label_short' => $decrypted['label'],
            'children' => count(Folder::all()->filter(fn(Folder $folder) => $folder->parent() === $this->data['id'])),
            'items' => count(Password::all()->filter(fn(Password $password) => $password->folder() === $this->data['id'])),
        ];
    }

    public function children(): Collection
    {
        return Folder::all()->filter(fn(Folder $folder) => $folder->parent() === $this->data['id']);
    }
}
