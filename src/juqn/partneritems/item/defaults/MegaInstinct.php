<?php

declare(strict_types=1);

namespace juqn\partneritems\item\defaults;

use juqn\partneritems\item\Item;
use pocketmine\block\utils\DyeColor;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class MegaInstinct extends Item
{
    
    /**
     * MegaInstinct construct.
     */
    public function __construct()
    {
        parent::__construct('&5Mega Instinct', VanillaItems::DYE()->setColor(DyeColor::YELLOW())); // Dont have dyecolorids
    }
    
    /**
     * @return int
     */
    public function getTime(): int
    {
        return 120;
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
            $effects->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 30, 0));
            $effects->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 30, 3));
            $effects->add(new EffectInstance(VanillaEffects::REGENERATION(), 20 * 30, 3));
            $effects->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 30, 3));
            
            $this->pop($player);
        }
        return $result;
    }
}