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

class MegaInstinct extends AbilityItem
{
    public function __construct()
    {
        $config = Provider::getAbilityConfig('megainstinct');
        parent::__construct(
            $config['name'] ?? "§e§lMega Instinct",
            VanillaItems::STICK(),
            $config['cooldown'] ?? 120,
            $config['lore'] ?? [
                "§7Versión mejorada del",
                "§7Ultra Instinct con más efectos",
                "",
                "§aClick derecho para usar"
            ]
        );
    }
    
    public function getAbilityName(): string
    {
        return "megainstinct";
    }
    
    protected function execute(Player $player, Vector3 $directionVector): void
    {
        $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 20, 3));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::JUMP_BOOST(), 20 * 20, 2));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 20, 1));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 20, 1));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 20 * 20, 1));
        
        $this->sendMessage($player, "§e¡Mega Instinct activado! Todos los efectos mejorados!");
    }
}