<?php

declare(strict_types=1);

namespace Phoenix4041\UltimateAbilities\item\abilities;

use Phoenix4041\UltimateAbilities\item\AbilityItem;
use Phoenix4041\UltimateAbilities\provider\Provider;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class AntiGapple extends AbilityItem
{
    public function __construct()
    {
        $config = Provider::getAbilityConfig('antigapple');
        parent::__construct(
            $config['name'] ?? "§4§lAnti Gapple",
            VanillaItems::ENCHANTED_GOLDEN_APPLE(),
            $config['cooldown'] ?? 25,
            $config['lore'] ?? [
                "§7Niega los efectos de",
                "§7manzanas doradas por 15 segundos",
                "",
                "§aClick derecho para usar"
            ]
        );
    }
    
    public function getAbilityName(): string
    {
        return "antigapple";
    }
    
    protected function execute(Player $player, Vector3 $directionVector): void
    {
        $session = \Phoenix4041\UltimateAbilities\UltimateAbilities::getInstance()->getSessionManager()->getSession($player);
        if ($session !== null) {
            $session->setEffect('antigapple', 15);
        }
        
        $this->sendMessage($player, "§4¡Anti Gapple activado! Los efectos de manzanas doradas son negados!");
    }
}