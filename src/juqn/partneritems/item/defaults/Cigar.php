<?php

declare(strict_types=1);

namespace juqn\partneritems\item\defaults;

use juqn\partneritems\item\Item;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class Cigar extends Item
{
    
    /**
     * Cigar construct.
     */
    public function __construct()
    {
        parent::__construct('&2Cigar', VanillaItems::BAMBOO());
    }
    
    /**
     * @return int
     */
    public function getTime(): int
    {
        return 90;
    }
    
    /**
     * @param Player $player
     * @param Vector3 $directionVector
     * @return ItemUseResult
     */
    public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult
    {
        $result = parent::onClickAir($player, $directionVector);
        
        if ($result->equals(ItemUseResult::SUCCESS())) {
            $effects = $player->getEffects();
            $effects->add(new EffectInstance(VanillaEffects::NAUSEA(), 20 * 10, 0));
            $effects->add(new EffectInstance(VanillaEffects::FIRE_RESISTANCE(), 20 * 10, 0));
            $effects->add(new EffectInstance(VanillaEffects::INSTANT_HEALTH(), 20 * 10, 4));
            $effects->add(new EffectInstance(VanillaEffects::HEALTH_BOOST(), 20 * 10, 3));
        
            $this->pop($player);
        }
        return $result;
    }
}