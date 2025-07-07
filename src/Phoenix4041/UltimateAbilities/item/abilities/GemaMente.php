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

class GemaMente extends AbilityItem
{
    public function __construct()
    {
        $config = Provider::getAbilityConfig('gemamente');
        parent::__construct(
            $config['name'] ?? "§9§lGema de Mente",
            VanillaItems::EGG(),
            $config['cooldown'] ?? 80,
            $config['lore'] ?? [
                "§7Controla la mente del",
                "§7jugador más cercano",
                "",
                "§aClick derecho para usar"
            ]
        );
    }
    
    public function getAbilityName(): string
    {
        return "gemamente";
    }
    
    protected function execute(Player $player, Vector3 $directionVector): void
    {
        $world = $player->getWorld();
        $pos = $player->getPosition();
        $nearestPlayer = null;
        $nearestDistance = 20.0;
        
        foreach ($world->getPlayers() as $target) {
            if ($target !== $player && $target->getPosition()->distance($pos) < $nearestDistance) {
                $nearestPlayer = $target;
                $nearestDistance = $target->getPosition()->distance($pos);
            }
        }
        
        if ($nearestPlayer !== null) {
            $nearestPlayer->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 10 * 20, 3));
            $nearestPlayer->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 5 * 20, 0));
            $nearestPlayer->getEffects()->add(new EffectInstance(VanillaEffects::WEAKNESS(), 10 * 20, 1));
            
            $nearestPlayer->sendMessage("§9¡Tu mente está siendo controlada por " . $player->getName() . "!");
            $this->sendMessage($player, "§9¡Gema de Mente activada! Controlando a " . $nearestPlayer->getName() . "!");
        } else {
            $this->sendMessage($player, "§c¡No hay jugadores cercanos para controlar!");
        }
    }
}