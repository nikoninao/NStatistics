<?php

declare(strict_types=1);

namespace statistics\provider\factory;

use statistics\provider\JsonProvider;
use statistics\provider\SqliteProvider;
use statistics\provider\StatsProvider;
use statistics\provider\YamlProvider;

class ProviderFactory {

    public static function create(string $type, string $dataFolder): StatsProvider {
        return match ($type) {
            'yaml' => new YamlProvider($dataFolder),
            'sqlite' => new SqliteProvider($dataFolder),
            'json' => new JsonProvider($dataFolder),
            default => throw new \InvalidArgumentException('Unknown provider '. $type),
        };
    }
}