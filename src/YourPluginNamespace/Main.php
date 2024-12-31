<?php

declare(strict_types=1);

namespace YourPluginNamespace;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;

class Main extends PluginBase {

    public function onEnable(): void {
        $this->getLogger()->info(TextFormat::GREEN . "TopCommand Plugin Enabled");
    }

    public function onDisable(): void {
        $this->getLogger()->info(TextFormat::RED . "TopCommand Plugin Disabled");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($command->getName() === "top") {
            if ($sender instanceof Player) {
                try {
                    $this->teleportToTop($sender);
                    $sender->sendMessage(TextFormat::GREEN . "Teleported to the highest block above you!");
                } catch (\Exception $e) {
                    $sender->sendMessage(TextFormat::RED . "An error occurred while trying to teleport you.");
                    $this->getLogger()->error("Error teleporting player: " . $e->getMessage());
                }
                return true;
            } else {
                $sender->sendMessage(TextFormat::RED . "This command can only be used in-game.");
                return false;
            }
        }
        return false;
    }

    private function teleportToTop(Player $player): void {
        $position = $player->getPosition();
        $level = $player->getWorld();
        $highestBlockY = $level->getHighestBlockAt((int)$position->getX(), (int)$position->getZ());
        if ($highestBlockY !== -1) {
            $player->teleport(new Vector3($position->getX(), $highestBlockY + 1, $position->getZ()));
        } else {
            throw new \Exception("Could not find the highest block at the player's position.");
        }
    }
}
