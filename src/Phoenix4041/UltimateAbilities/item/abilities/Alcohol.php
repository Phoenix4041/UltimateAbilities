<?php

declare(strict_types=1);

namespace Phoenix4041\UltimateAbilities\item\abilities;

use Phoenix4041\UltimateAbilities\item\AbilityItem;
use Phoenix4041\UltimateAbilities\provider\Provider;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class Alcohol extends AbilityItem
{
    public function __construct()
    {
        $config = Provider::getAbilityConfig('alcohol');
        parent::__construct(
            $config['name'] ?? "§5§lAlcohol",
            VanillaItems::POTION(),
            $config['cooldown'] ?? 30,
            $config['lore'] ?? [
                "§7Te da visión nocturna pero",
                "§7reduce tu precisión",
                "",
                "§aClick derecho para usar"
            ]
        );
    }
    
    public function getAbilityName(): string
    {
        return "alcohol";
    }
    
    protected function execute(Player $player, Vector3 $directionVector): void
    {
        $player->getEffects()->add(new EffectInstance(VanillaEffects::NIGHT_VISION(), 60 * 20, 0));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::NAUSEA(), 15 * 20, 0));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 20 * 20, 0));
        
        $this->sendMessage($player, "§5¡Alcohol activado! Visión nocturna pero con efectos secundarios!");
    }
}