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

class Fenix extends AbilityItem
{
    public function __construct()
    {
        $config = Provider::getAbilityConfig('fenix');
        parent::__construct(
            $config['name'] ?? "§c§lFénix",
            VanillaItems::GHAST_TEAR(),
            $config['cooldown'] ?? 90,
            $config['lore'] ?? [
                "§7Regenera tu vida completa",
                "§7y te da resistencia al fuego",
                "",
                "§aClick derecho para usar"
            ]
        );
    }
    
    public function getAbilityName(): string
    {
        return "fenix";
    }
    
    protected function execute(Player $player, Vector3 $directionVector): void
    {
        $player->setHealth($player->getMaxHealth());
        $player->getEffects()->add(new EffectInstance(VanillaEffects::FIRE_RESISTANCE(), 60 * 20, 0));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 30 * 20, 1));
        
        $this->sendMessage($player, "§c¡Fénix activado! Vida completa y resistencia al fuego!");
    }
}
