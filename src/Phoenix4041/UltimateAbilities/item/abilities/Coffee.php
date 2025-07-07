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
use pocketmine\world\particle\SmokeParticle;
use pocketmine\world\sound\FizzSound;

class Coffee extends AbilityItem
{
    public function __construct()
    {
        $config = Provider::getAbilityConfig('coffee');
        parent::__construct(
            $config['name'] ?? "§6§lCoffee",
            VanillaItems::BROWN_DYE(),
            $config['cooldown'] ?? 50,
            $config['lore'] ?? [
                "§7Te da energía y velocidad",
                "§7de minado durante 45 segundos",
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
        $player->getEffects()->add(new EffectInstance(VanillaEffects::HASTE(), 45 * 20, 2));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::JUMP_BOOST(), 45 * 20, 0));
        
        $world = $player->getWorld();
        $pos = $player->getPosition();
        
        for ($i = 0; $i < 10; $i++) {
            $x = $pos->x + (mt_rand(-5, 5) / 10);
            $y = $pos->y + (mt_rand(0, 10) / 10);
            $z = $pos->z + (mt_rand(-5, 5) / 10);
            $world->addParticle(new Vector3($x, $y, $z), new SmokeParticle());
        }
        
        $world->addSound($pos, new FizzSound());
        $this->sendMessage($player, "§6¡Coffee activado! Energía y velocidad de minado mejoradas!");
    }
}