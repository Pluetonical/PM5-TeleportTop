<?php

declare(strict_types=1);

namespace Top;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;
use pocketmine\scheduler\ClosureTask;

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
                $this->startCountdown($sender);
                return true;
            } else {
                $sender->sendMessage(TextFormat::RED . "This command can only be used in-game.");
                return false;
            }
        }
        return false;
    }

    private function startCountdown(Player $player): void {
        $countdown = 5; // Countdown time in seconds
        $taskHandler = null;
        $taskHandler = $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function() use ($player, &$countdown, &$taskHandler): void {
            if ($countdown > 0) {
                $player->sendPopup(TextFormat::YELLOW . "Teleporting in " . $countdown . "...");
                $countdown--;
            } else {
                try {
                    $this->teleportToTop($player);
                    $player->sendMessage(TextFormat::GREEN . "Teleported to the highest block above you!");
                } catch (\Exception $e) {
                    $player->sendMessage(TextFormat::RED . "An error occurred while trying to teleport you.");
                    $this->getLogger()->error("Error teleporting player: " . $e->getMessage());
                }
                $taskHandler->cancel();
            }
        }), 20); // 20 ticks = 1 second
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
