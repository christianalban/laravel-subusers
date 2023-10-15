<?php
namespace Alban\LaravelSubusers;

class Subusers
{
    static $beforeUserCallback = null;

    public static function beforeUser(callable $callback) {
        static::$beforeUserCallback = $callback;
    }
}
