<?php

declare(strict_types=1);

namespace juqn\partneritems\item\defaults;

use JetBrains\PhpStorm\Pure;
use juqn\partneritems\entity\PortableFactionBardEntity;
use juqn\partneritems\item\Item;
use juqn\partneritems\PartnerItems;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class PortableFactionBard extends Item
{
    
    /**
     * Alcohol construct.
     */
    public function __construct()
    {
        parent::__construct('&bPortableFactionBard', VanillaItems::NETHER_STAR());
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
            $bard = new PortableFactionBardEntity($player->getLocation());
            $bard->setOwner($player);
            $bard->setPos($player->getLocation());
            $bard->spawnToAll();
            PartnerItems::$bard_allow[$player->getName()] = true;
        
            $this->pop($player);
        }
        return $result;
    }

    #[Pure] public static function isAllow(Player $player): bool
    {
        if(!isset(PartnerItems::$bard_allow[$player->getName()])){
            return false;
        }
        return true;
    }

    public static function setAllow(Player $player){
        PartnerItems::$bard_allow[$player->getName()] = true;
    }

    public static function removeAllow(Player $player){
        unset(PartnerItems::$bard_allow[$player->getName()]);
    }
}