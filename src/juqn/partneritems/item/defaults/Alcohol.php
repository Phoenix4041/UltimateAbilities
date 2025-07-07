<?php

declare(strict_types=1);

namespace juqn\partneritems\item\defaults;

use juqn\partneritems\item\Item;
use pocketmine\data\bedrock\PotionTypeIds;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\item\PotionType;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class Alcohol extends Item
{
    
    /**
     * Alcohol construct.
     */
    public function __construct()
    {
        parent::__construct('&dAlcohol', VanillaItems::POTION()->setType(PotionType::NIGHT_VISION()));
    }
    
    /**
     * @return int
     */
    public function getTime(): int
    {
        return 60;
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
            $effects->add(new EffectInstance(VanillaEffects::NAUSEA(), 20 * 10, 1));
            $effects->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 10, 4));
            $effects->add(new EffectInstance(VanillaEffects::SLOWNESS(), 20 * 10, 0));
        
            $this->pop($player);
        }
        return $result;
    }
}