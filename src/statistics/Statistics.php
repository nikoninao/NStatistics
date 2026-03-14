<?php

declare(strict_types=1);

namespace statistics;

use CortexPE\Commando\exception\HookAlreadyRegistered;
use CortexPE\Commando\PacketHooker;
use NetherByte\PlaceholderAPI\PlaceholderAPI;
use pocketmine\plugin\PluginBase;

use statistics\command\StatsCommand;
use statistics\manager\PlayerStatsManager;
use statistics\placeholder\StatsPlaceholderProvider;
use statistics\provider\factory\ProviderFactory;
use statistics\provider\StatsProvider;
use statistics\listener\EventListener;

final class Statistics extends PluginBase {

    protected StatsProvider $provider;
    protected PlayerStatsManager $playerStatsManager;

    /**
     * @throws HookAlreadyRegistered
     */
    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->loadProvider();
        $this->loadManagers();
        $this->loadPlaceholders();
        $this->registerCommands();
        $this->loadListeners();
    }

    private function loadProvider(): void {
        $this->provider = ProviderFactory::create(
            $this->getConfig()->get('provider', 'yaml'),
            $this->getDataFolder(),
        );
    }

    private function loadPlaceholders(): void {
        $placeholderAPI = $this->getServer()->getPluginManager()->getPlugin("PlaceholderAPI_NB");
        if ($placeholderAPI !== null) {
            PlaceholderAPI::registerProvider(
                new StatsPlaceholderProvider($this)
            );
            $this->getLogger()->info("Statistics provider registered!");
        } else {
            $this->getLogger()->warning("PlaceholderAPI_NB not found. Placeholders won't be available.");
        }
    }

    private function loadManagers(): void {
        $this->playerStatsManager = new PlayerStatsManager($this->getProvider());
    }

    /**
     * @throws HookAlreadyRegistered
     */
    public function registerCommands(): void {
        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }
        $this->getServer()->getCommandMap()->register(
            'statistics',
            new StatsCommand($this),
        );
    }

    private function loadListeners(): void {
        $this->getServer()->getPluginManager()->registerEvents(
            new EventListener($this->getPlayerStatsManager()),
            $this,
        );
    }

    public function getProvider(): StatsProvider {
        return $this->provider;
    }

    public function getPlayerStatsManager(): PlayerStatsManager {
        return $this->playerStatsManager;
    }

    public function getMessage(string $key, array $replacements = []): string {
        $message = $this->getConfig()->get("messages")[$key];
        foreach ($replacements as $placeholder => $value) {
            $message = str_replace("{{$placeholder}}", (string)$value, $message);
        }
        return $message;
    }

}