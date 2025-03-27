<?php

namespace App;

use App\Exceptions\UnauthorizedException;
use Illuminate\Support\Facades\Cache;
use SensitiveParameter;
use SensitiveParameterValue;

class Master
{
    private static SensitiveParameterValue $master;

    public static function ask(): SensitiveParameterValue
    {
        if (Cache::has('master')) {
            try {
                self::$master = new SensitiveParameterValue(decrypt(Cache::get('master')));
            } catch(\Exception) {
                
            }
        }

        if (!isset(self::$master) && app()->runningInConsole()) {
            self::set(Cli::get()->secret('What is the master password?'));
        }

        if (!isset(self::$master)) {
            throw new UnauthorizedException("Enter your master password.");
        }

        return self::$master;
    }

    public static function empty(): bool
    {
        return !Cache::has('master');
    }

    public static function set(#[SensitiveParameter] string $master): void
    {
        self::$master = new SensitiveParameterValue($master);

        Cache::put('master', encrypt(self::$master->getValue()), now()->addMinutes(5));
    }

    public static function saveLife(): void
    {
        self::set(self::ask()->getValue());
    }
}
