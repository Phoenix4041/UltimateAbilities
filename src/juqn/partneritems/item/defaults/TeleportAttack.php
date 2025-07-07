<?php

declare(strict_types=1);

namespace juqn\partneritems\item\defaults;

use juqn\partneritems\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class TeleportAttack extends Item
{
    
    /**
     * Alcohol construct.
     */
    public function __construct()
    {
        parent::__construct('&eTeleport Attack', VanillaItems::FEATHER());
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
        $target = null;
        
        foreach ($player->getServer()->getOnlinePlayers() as $tt) {
            $distance = $player->getPosition()->distance($tt->getPosition());
            
            if ($distance <= 20) {
                $target = $tt;
                break;
            }
        }
        
        if ($target === null) {
            $player->sendMessage(TextFormat::colorize('&cJugadores no encontrados dentro de un radio de 20 bloques'));
            return ItemUseResult::FAIL();
        }
        $result = parent::onClickAir($player, $directionVector);
        
        if ($result->equals(ItemUseResult::SUCCESS())) {
            $player->teleport($target->getPosition());
            $this->pop($player);
        }
        return $result;
    }
}