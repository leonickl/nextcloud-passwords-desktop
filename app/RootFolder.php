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
        ]);
    }

    public function decrypt(): array
    {
        return $this->data;
    }
}
