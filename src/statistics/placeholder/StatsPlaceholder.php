<?php

declare(strict_types=1);

namespace statistics\placeholder;

use NetherByte\PlaceholderAPI\expansion\Expansion;
use pocketmine\player\Player;
use statistics\Statistics;

final class StatsPlaceholder extends Expansion {

    public function __construct(Statistics $plugin) {
        $this->plugin = $plugin;
        parent::__construct($plugin);
    }

    public function getName(): string {
        return 'Statistics';
    }

    public function getAuthor(): ?string {
        return 'nikonana';
    }

    public function getVersion(): ?string {
        return '1.0.0';
    }

    public function getDescription(): ?string {
        return 'Provides statistics placeholders';
    }

    public function onRequest(string $identifier, ?Player $player): ?string {
        if ($player === null) {
            return '';
        }

        $stats = $this->plugin->getPlayerStatsManager()->get($player->getName());

        return match($identifier) {
            'kills' => (string) $stats->kills,
            'deaths' => (string) $stats->deaths,
            default => null
        };
    }
}