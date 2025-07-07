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

class GemaPoder extends AbilityItem
{
    public function __construct()
    {
        $config = Provider::getAbilityConfig('gemapoder');
        parent::__construct(
            $config['name'] ?? "§a§lGema del Poder",
            VanillaItems::EMERALD(),
            $config['cooldown'] ?? 100,
            $config['lore'] ?? [
                "§7Otorga múltiples efectos",
                "§7de poder durante 20 segundos",
                "",
                "§aClick derecho para usar"
            ]
        );
    }
    
    public function getAbilityName(): string
    {
        return "gemapoder";
    }
    
    protected function execute(Player $player, Vector3 $directionVector): void
    {
        $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 20, 2));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 20, 2));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 20, 1));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 20 * 20, 1));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::JUMP_BOOST(), 20 * 20, 1));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::FIRE_RESISTANCE(), 20 * 20, 0));
        
        $this->sendMessage($player, "§a¡Gema del Poder activada! Múltiples efectos de poder otorgados!");
    }
}