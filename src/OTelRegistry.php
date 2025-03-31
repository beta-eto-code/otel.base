<?php

namespace Otel\Base;

use Otel\Base\Interfaces\OTelSpanManagerInterface;

class OTelRegistry
{
    /**
     * @var array<OTelSpanManagerInterface>
     */
    private static array $registry = [];

    public static function register(string $name, OTelSpanManagerInterface $class): void
    {
        if (self::has($name)) {
            throw new \Exception("Class $name already registered");
        }

        self::$registry[$name] = $class;
    }

    public static function get($name): OTelSpanManagerInterface
    {
        return self::$registry[$name];
    }

    public static function has($name): bool
    {
        return isset(self::$registry[$name]);
    }

    public static function all(): array
    {
        return self::$registry;
    }
}
