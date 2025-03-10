<?php

namespace App;

use App\Exceptions\UnauthorizedException;
use Closure;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Foundation\Console\ClosureCommand;

class Runtime
{
    /**
     * @noinspection PhpRedundantCatchClauseInspection
     */
    public static function run(Closure $closure, ClosureCommand $context, array $args): void
    {
        Cli::set($context);

        while (true) {
            try {
                $closure(...$args);
                return;
            } catch (UnauthorizedException) {
                Client::login();
            } catch(ConnectException) {
                $context->error('No internet connection available.');
            }
        }
    }
}
