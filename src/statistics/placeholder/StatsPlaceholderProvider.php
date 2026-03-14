<?php

declare(strict_types=1);

namespace statistics\placeholder;

use NetherByte\PlaceholderAPI\expansion\Expansion;
use NetherByte\PlaceholderAPI\provider\Provider;
use statistics\Statistics;

final class StatsPlaceholderProvider implements Provider {

    private Statistics $plugin;

    public function __construct(Statistics $plugin) {
        $this->plugin = $plugin;
    }

    public function getName(): string {
        return 'Statistics';
    }

    public function listExpansions(): array {
        return ['stats'];
    }

    public function provide(string $identifier): ?Expansion {
        if ($identifier === 'stats') {
            return new StatsPlaceholder($this->plugin);
        }
        return null;
    }
}