<?php

declare(strict_types=1);

namespace Phoenix4041\UltimateAbilities\command;

use Phoenix4041\UltimateAbilities\UltimateAbilities;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class UltimateAbilitiesCommand extends Command
{
    public function __construct()
    {
        parent::__construct("ultimateabilities", "Ultimate Abilities command", "/ultimateabilities <give|list> [player] [ability]", ["ua", "abilities"]);
        $this->setPermission("ultimateabilities.give");
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        if (!$sender->hasPermission("ultimateabilities.give")) {
            $sender->sendMessage("§cNo tienes permisos para usar este comando.");
            return false;
        }
        
        if (empty($args)) {
            $this->sendUsage($sender);
            return false;
        }
        
        $subCommand = strtolower($args[0]);
        
        switch ($subCommand) {
            case "give":
                return $this->handleGiveCommand($sender, $args);
            case "list":
                return $this->handleListCommand($sender);
            default:
                $this->sendUsage($sender);
                return false;
        }
    }
    
    private function handleGiveCommand(CommandSender $sender, array $args): bool
    {
        if (count($args) < 3) {
            $sender->sendMessage("§cUso: /ultimateabilities give <jugador> <habilidad>");
            return false;
        }
        
        $playerName = $args[1];
        $abilityName = strtolower($args[2]);
        
        $player = Server::getInstance()->getPlayerByPrefix($playerName);
        if ($player === null) {
            $sender->sendMessage("§cEl jugador §e{$playerName} §cno está conectado.");
            return false;
        }
        
        $itemManager = UltimateAbilities::getInstance()->getItemManager();
        $item = $itemManager->getAbilityItem($abilityName);
        
        if ($item === null) {
            $sender->sendMessage("§cLa habilidad §e{$abilityName} §cno existe.");
            $sender->sendMessage("§7Usa §a/ultimateabilities list §7para ver todas las habilidades.");
            return false;
        }
        
        $player->getInventory()->addItem($item);
        $sender->sendMessage("§aHas dado la habilidad §e{$abilityName} §aa §e{$player->getName()}§a.");
        $player->sendMessage("§aHas recibido la habilidad §e{$abilityName}§a.");
        
        return true;
    }
    
    private function handleListCommand(CommandSender $sender): bool
    {
        $itemManager = UltimateAbilities::getInstance()->getItemManager();
        $abilities = $itemManager->getAbilityNames();
        
        $sender->sendMessage("§8=== §bHabilidades Disponibles §8===");
        foreach ($abilities as $ability) {
            $sender->sendMessage("§7- §e{$ability}");
        }
        
        return true;
    }
    
    private function sendUsage(CommandSender $sender): void
    {
        $sender->sendMessage("§8=== §bUltimate Abilities §8===");
        $sender->sendMessage("§e/ultimateabilities give <jugador> <habilidad> §7- Da una habilidad a un jugador");
        $sender->sendMessage("§e/ultimateabilities list §7- Lista todas las habilidades disponibles");
    }
}