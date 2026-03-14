<?php

declare(strict_types=1);

namespace statistics\command;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use statistics\Statistics;

class StatsCommand extends BaseCommand {
    public function __construct(Statistics $plugin) {
        parent::__construct(
            $plugin,
            'stats',
            'Show statistics',
            ['usr', 'statistics']
        );
        $this->setPermission('statistics.command');
    }

    /**
     * @throws ArgumentOrderException
     */
    public function prepare(): void {
        $this->registerArgument(0, new RawStringArgument('player', true));
        $this->registerArgument(1, new RawStringArgument('type', true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (!$sender instanceof Player) {
            return;
        }

        /** @var Statistics $plugin */
        $plugin = $this->getOwningPlugin();

        $targetName = $args['player'] ?? $sender->getName();
        $type = $args['type'] ?? null;

        $stats = $plugin->getPlayerStatsManager()->get($targetName);
        if ($stats === null) {
            $sender->sendMessage($plugin->getMessage('player-not-found', ['player' => $targetName]));
        }

        if ($type === null) {
            $sender->sendMessage($plugin->getMessage('stats-header', ['player' => $targetName]));
            $sender->sendMessage($plugin->getMessage('kills', ['count' => $stats->kills]));
            $sender->sendMessage($plugin->getMessage('deaths', ['count' => $stats->deaths]));
            $sender->sendMessage($plugin->getMessage('kdr', ['count' => $this->formatKd($stats->kills, $stats->deaths)]));
            return;
        }

        $type = strtolower($type);

        match ($type) {
            'kills' => $sender->sendMessage($plugin->getMessage('kills', ['count' => $stats->kills])),
            'deaths' => $sender->sendMessage($plugin->getMessage('deaths', ['count' => $stats->deaths])),
            'kdr', 'kd' => $sender->sendMessage($plugin->getMessage('kdr', ['count' => $this->formatKd($stats->kills, $stats->deaths)])),
            default => $sender->sendMessage($plugin->getMessage('invalid-type')),
        };
    }

    private function formatKd(int $kills, int $deaths): string {
        if ($deaths === 0) {
            return $kills > 0 ? number_format($kills, 2) : '0.00';
        }
        return number_format($kills / $deaths, 2);
    }

    public function getPermission() {
        // TODO: Implement getPermission() method.
    }
}