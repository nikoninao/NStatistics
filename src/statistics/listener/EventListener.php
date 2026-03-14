<?php

declare(strict_types=1);

namespace statistics\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\player\Player;
use statistics\manager\PlayerStatsManager;

final class EventListener implements Listener {

    public function __construct(
        private PlayerStatsManager $stats
    ) {}

    public function onJoin(PlayerJoinEvent $event): void {
        $name = $event->getPlayer()->getName();
        $this->stats->load($name);
    }

    public function onDeath(PlayerDeathEvent $event): void {
        $victim = $event->getEntity();
        if (!$victim instanceof Player) return;

        $this->stats->addDeath($victim->getName());
        $this->stats->save($victim->getName());

        $damage = $victim->getLastDamageCause();
        if (!$damage instanceof EntityDamageByEntityEvent) return;

        $killer = $damage->getDamager();
        if (!$killer instanceof Player) return;

        $this->stats->addKill($killer->getName());
        $this->stats->save($killer->getName());
    }
}