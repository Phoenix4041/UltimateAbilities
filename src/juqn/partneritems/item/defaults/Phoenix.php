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

class Phoenix extends Item
{
    
    /**
     * Phoenix construct.
     */
    public function __construct()
    {
        parent::__construct('&6Phoenix', VanillaItems::GHAST_TEAR());
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
            $effects->add(new EffectInstance(VanillaEffects::ABSORPTION(), 20 * 15, 4));
            $effects->add(new EffectInstance(VanillaEffects::REGENERATION(), 20 * 15, 4));
        
            $this->pop($player);
        }
        return $result;
    }
}