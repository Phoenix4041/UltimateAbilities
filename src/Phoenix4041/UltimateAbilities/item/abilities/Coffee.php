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

class Coffee extends AbilityItem
{
    public function __construct()
    {
        $config = Provider::getAbilityConfig('coffee');
        parent::__construct(
            $config['name'] ?? "§6§lCoffee",
            VanillaItems::COCOA_BEANS(),
            $config['cooldown'] ?? 60,
            $config['lore'] ?? [
                "§7Te da energía y velocidad",
                "§7para minar más rápido",
                "",
                "§aClick derecho para usar"
            ]
        );
    }
    
    public function getAbilityName(): string
    {
        return "coffee";
    }
    
    protected function execute(Player $player, Vector3 $directionVector): void
    {
        $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 45 * 20, 1));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::HASTE(), 45 * 20, 1));
        
        $this->sendMessage($player, "§6¡Coffee activado! Speed y Haste por 45 segundos!");
    }
}