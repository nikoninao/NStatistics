<?php

declare(strict_types=1);

namespace statistics\provider;

use pocketmine\utils\Config;
use statistics\model\PlayerStats;

class JsonProvider implements StatsProvider {

    private Config $config;

    public function __construct(string $folder){
        $this->config = new Config($folder . "stats.json", Config::JSON);
    }

    public function load(string $player): PlayerStats{
        $data = $this->config->get(strtolower($player), []);

        return new PlayerStats(
            $data["kills"] ?? 0,
            $data["deaths"] ?? 0,
        );
    }

    /**
     * @throws \JsonException
     */
    public function save(string $player, PlayerStats $stats): void{
        $this->config->set(strtolower($player), [
            "kills" => $stats->kills,
            "deaths" => $stats->deaths,
        ]);

        $this->config->save();
    }

}