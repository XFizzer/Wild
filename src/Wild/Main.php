<?php

namespace Wild;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener
{
    private $inWild = [];

    public function onEnable()
    {
        $this->getLogger()->info("Wild by XFizzer loaded!");
    }

    public function onDisable()
    {
        $this->getLogger()->info("Wild by XFizzer disable!");
    }

    /**
     * @param CommandSender $sender
     * @param Command $command
     * @param string $label
     * @param array $args
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        switch ($command->getName()) {
            case "wild":
                if (!$sender instanceof Player) {
                    $sender->sendMessage("You can only use this command in game!");
                    return false;
                }
                if (!$sender->hasPermission("wild.use")) {
                    $sender->sendMessage("You don't have permission to use this command!");
                    return false;
                }
                $x = rand(2, 1400);
                $z = rand(2, 1400);
                $sender->teleport(new Position($x, 128, $z, $sender->getLevel()));
                $sender->sendMessage("§aTeleported to wild.");
                $this->inWild[] = $sender->getName();
                break;
        }
        return true;
    }

    /**
     * @param EntityDamageEvent $event
     */
    public function onDamage(EntityDamageEvent $event)
    {
        $entity = $event->getEntity();
        if (!$entity instanceof Player) return;
        if ($entity->getLastDamageCause() === EntityDamageEvent::CAUSE_FALL) {
            if (in_array($entity->getName(), $this->inWild)) {
                unset($this->inWild[array_search($entity->getName(), $this->inWild)]);
                $event->setCancelled(true);
            }
        }
    }
}