<?php

declare(strict_types=1);

namespace Phoenix4041\UltimateAbilities\listener;

use Phoenix4041\UltimateAbilities\item\abilities\AntiGapple;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\player\Player;

class AntiGappleListener implements Listener
{
    public function onEntityDamageByEntity(EntityDamageByEntityEvent $event): void
    {
        $damager = $event->getDamager();
        $target = $event->getEntity();
        
        if (!($damager instanceof Player)) {
            return;
        }
        
        $item = $damager->getInventory()->getItemInHand();
        
        if ($item->hasCustomName() && 
            (strpos($item->getCustomName(), "Anti Gapple") !== false ||
             strpos($item->getCustomName(), "§4§lAnti Gapple") !== false)) {
            
            $antiGapple = new AntiGapple();
            $antiGapple->onAttack($damager, $target, $event);
        }
    }
}