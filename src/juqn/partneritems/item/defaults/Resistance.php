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

class Resistance extends Item
{
    
    /**
     * Strength construct.
     */
    public function __construct()
    {
        parent::__construct('&1Resistance', VanillaItems::IRON_INGOT());
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
            $effects->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 15, 2));
        
            $this->pop($player);
        }
        return $result;
    }
}