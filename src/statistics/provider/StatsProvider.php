<?php

declare(strict_types=1);

namespace statistics\provider;

use statistics\model\PlayerStats;

interface StatsProvider {

    public function load(string $player): PlayerStats;

    public function save(string $player, PlayerStats $stats): void;

}