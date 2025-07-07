<?php

declare(strict_types=1);

namespace juqn\partneritems\item\defaults;

use juqn\partneritems\entity\SwitcherEntity;
use juqn\partneritems\item\Item;
use pocketmine\data\bedrock\PotionTypeIds;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Location;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\Listener;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;
use pocketmine\world\Position;

class Switcher extends Item implements Listener
{
    
    /**
     * Alcohol construct.
     */
    public function __construct()
    {
        parent::__construct('&dSwitcher', VanillaItems::SNOWBALL());
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
            $entity = new SwitcherEntity(Location::fromObject($player->getEyePos(), $player->getWorld(), $player->getLocation()->getYaw(), $player->getLocation()->getPitch()), $player);
            $entity->setMotion($directionVector->multiply(1.5));
            $entity->spawnToAll();

            $this->pop($player);
        }
        return $result;
    }

    public function onHitByProjectile(ProjectileHitEntityEvent $event) : void
    {
        $hit = $event->getEntityHit();
        if ($hit instanceof Player) {
            $entity = $event->getEntity();
            $player = $entity->getOwningEntity();
            if ($player instanceof Player) {
                if ($entity instanceof SwitcherEntity) {
                    $pos1 = $player->getPosition();
                    $pos2 = $hit->getPosition();
                    if ($pos1 instanceof Position)
                        $hit->teleport($pos1);
                    self::playSound($pos1, "mob.endermite.hit");
                    self::playSound($pos2, "mob.endermite.hit");
                    $player->teleport($pos2);
                }
            }
        }
    }

    protected static function playSound(Position $pos, string $soundName):void {
        $sPk = new PlaySoundPacket();
        $sPk->soundName = $soundName;
        $sPk->x = $pos->x;
        $sPk->y = $pos->y;
        $sPk->z = $pos->z;
        $sPk->volume = $sPk->pitch = 1;
        $pos->getWorld()->broadcastPacketToViewers($pos, $sPk);
    }


}