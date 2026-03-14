<?php

declare(strict_types=1);

namespace statistics\manager;

use statistics\model\PlayerStats;
use statistics\provider\StatsProvider;

class PlayerStatsManager {

    private StatsProvider $provider;

    /** @var PlayerStats[] */
    private array $cache = [];

    public function __construct(StatsProvider $provider) {
        $this->provider = $provider;
    }

    public function load(string $player): void {
        $this->cache[$player] = $this->provider->load($player);
    }

    public function get(string $player): ?PlayerStats {
        return $this->cache[$player] ?? null;
    }

    public function save(string $player): void {
        $this->provider->save($player, $this->cache[$player]);
    }

    /**
     * Increments the player's death count by 1.
     *
     * This method retrieves the player's statistics from the cache
     * and increases the deaths counter.
     *
     * @param string $player
     * @return void
     */
    public function addDeath(string $player): void{
        $this->cache[$player]->deaths++;
    }

    /**
     * Increments the player's death count by 1.
     *
     * This method retrieves the player's statistics from the cache
     * and increases the kills counter.
     *
     * @param string $player
     * @return void
     */
    public function addKill(string $player): void{
        $this->cache[$player]->kills++;
    }

}