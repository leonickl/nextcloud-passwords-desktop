<?php

namespace App;

readonly class RootFolder extends Folder
{
    public function __construct()
    {
        parent::__construct([
            'id' => '00000000-0000-0000-0000-000000000000',
            'parent_id' => null,
            'label' => 'Root',
            'color' => 'bg-gray-700',
            'color_fg' => 'text-gray-700',
        ]);
    }

    public function decrypt(): array
    {
        return [
            'children' => count(Folder::all()->filter(fn(Folder $folder) => $folder->parent() === $this->data['id'])),
            'items' => count(Password::all()->filter(fn(Password $password) => $password->folder() === $this->data['id'])),
            ...$this->data,
        ];
    }
}
