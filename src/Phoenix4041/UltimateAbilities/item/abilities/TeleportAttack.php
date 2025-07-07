<?php

declare(strict_types=1);

namespace Phoenix4041\UltimateAbilities\item\abilities;

use Phoenix4041\UltimateAbilities\item\AbilityItem;
use Phoenix4041\UltimateAbilities\provider\Provider;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\particle\EndermanTeleportParticle;
use pocketmine\world\sound\EndermanTeleportSound;

class TeleportAttack extends AbilityItem
{
    public function __construct()
    {
        $config = Provider::getAbilityConfig('teleportattack');
        parent::__construct(
            $config['name'] ?? "§e§lTeleport Attack",
            VanillaItems::FEATHER(),
            $config['cooldown'] ?? 20,
            $config['lore'] ?? [
                "§7Teletransportate detrás",
                "§7del jugador más cercano",
                "",
                "§aClick derecho para usar"
            ]
        );
    }
    
    public function getAbilityName(): string
    {
        return "teleportattack";
    }
    
    protected function execute(Player $player, Vector3 $directionVector): void
    {
        $world = $player->getWorld();
        $pos = $player->getPosition();
        
        // Buscar el jugador más cercano
        $nearestPlayer = null;
        $nearestDistance = PHP_FLOAT_MAX;
        
        foreach ($world->getPlayers() as $target) {
            if ($target === $player) continue;
            
            $distance = $pos->distance($target->getPosition());
            if ($distance < $nearestDistance && $distance <= 20) { // Máximo 20 bloques
                $nearestDistance = $distance;
                $nearestPlayer = $target;
            }
        }
        
        if ($nearestPlayer === null) {
            $this->sendMessage($player, "§cNo hay jugadores cercanos para teletransportarse.");
            return;
        }
        
        // Calcular posición detrás del jugador
        $targetPos = $nearestPlayer->getPosition();
        $targetDirection = $nearestPlayer->getDirectionVector();
        
        // Posición detrás del jugador (2 bloques atrás)
        $teleportPos = $targetPos->subtract($targetDirection->multiply(2));
        $teleportPos = $teleportPos->add(0, 1, 0); // Un bloque hacia arriba
        
        // Efectos visuales antes del teletransporte
        $world->addParticle($pos, new EndermanTeleportParticle());
        $world->addSound($pos, new EndermanTeleportSound());
        
        // Teletransportar
        $player->teleport($teleportPos);
        
        // Efectos visuales después del teletransporte
        $world->addParticle($player->getPosition(), new EndermanTeleportParticle());
        $world->addSound($player->getPosition(), new EndermanTeleportSound());
        
        $this->sendMessage($player, "§e¡Te has teletransportado detrás de §b{$nearestPlayer->getName()}§e!");
        $this->sendMessage($nearestPlayer, "§c¡§e{$player->getName()} §cse ha teletransportado detrás de ti!");
    }
}