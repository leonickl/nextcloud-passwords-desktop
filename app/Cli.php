<?php

namespace App;

use Illuminate\Foundation\Console\ClosureCommand;

class Cli
{
    private static ClosureCommand $instance;

    public static function set(ClosureCommand $instance): void
    {
        self::$instance = $instance;
    }

    public static function get(): ClosureCommand
    {
        return self::$instance;
    }
}
