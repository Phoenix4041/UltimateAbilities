<?php

declare(strict_types=1);

namespace Phoenix4041\UltimateAbilities\item\abilities;

use Phoenix4041\UltimateAbilities\item\AbilityItem;
use Phoenix4041\UltimateAbilities\provider\Provider;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\particle\EndermanTeleportParticle;
use pocketmine\world\sound\EndermanTeleportSound;

class Switcher extends AbilityItem
{
    public function __construct()
    {
        $config = Provider::getAbilityConfig('switcher');
        parent::__construct(
            $config['name'] ?? "§b§lSwitcher",
            VanillaItems::SNOWBALL(),
            $config['cooldown'] ?? 25,
            $config['lore'] ?? [
                "§7Intercambia posiciones con",
                "§7el jugador que golpees",
                "",
                "§aGolpea a un jugador para usar"
            ]
        );
    }
    
    public function getAbilityName(): string
    {
        return "switcher";
    }
    
    protected function execute(Player $player, Vector3 $directionVector): void
    {
        $this->sendMessage($player, "§bGolpea a un jugador para intercambiar posiciones!");
    }
    
    public function onAttack(Player $damager, Entity $victim, EntityDamageByEntityEvent $event): void
    {
        if (!$victim instanceof Player) {
            return;
        }
        
        // Check cooldown
        if ($this->isOnCooldown($damager)) {
            $remaining = $this->getRemainingCooldown($damager);
            $damager->sendMessage("§cDebes esperar §e{$remaining}s §cpara usar Switcher.");
            return;
        }
        
        $damagerPos = $damager->getPosition();
        $victimPos = $victim->getPosition();
        
        // Efectos visuales antes del intercambio
        $damager->getWorld()->addParticle($damagerPos, new EndermanTeleportParticle());
        $victim->getWorld()->addParticle($victimPos, new EndermanTeleportParticle());
        
        $damager->getWorld()->addSound($damagerPos, new EndermanTeleportSound());
        $victim->getWorld()->addSound($victimPos, new EndermanTeleportSound());
        
        // Intercambiar posiciones
        $damager->teleport($victimPos);
        $victim->teleport($damagerPos);
        
        // Más efectos después del intercambio
        $damager->getWorld()->addParticle($damager->getPosition(), new EndermanTeleportParticle());
        $victim->getWorld()->addParticle($victim->getPosition(), new EndermanTeleportParticle());
        
        $this->sendMessage($damager, "§b¡Has intercambiado posiciones con §e{$victim->getName()}§b!");
        $this->sendMessage($victim, "§b¡§e{$damager->getName()} §bha intercambiado posiciones contigo!");
        
        // Set cooldown
        $this->setCooldown($damager);
        
        // Cancel original damage
        $event->cancel();
    }
}