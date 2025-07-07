<?php

namespace juqn\partneritems\entity;

use JetBrains\PhpStorm\Pure;
use juqn\partneritems\item\defaults\PortableFactionBard;
use juqn\partneritems\PartnerItems;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;
use Ayzrix\SimpleFaction\API\FactionsAPI;

class PortableFactionBardEntity extends Living {

    private $owner = null;
    private int $count_down = 50;//50 seconds
    private int $time = 0;
    private Position $pos;

    #[Pure] protected function getInitialSizeInfo(): EntitySizeInfo {
        return new EntitySizeInfo(1.9 , 0.6, 1.8);
    }

    public static function getNetworkTypeId(): string {
        return EntityIds::SNOW_GOLEM;
    }

    public function getName(): string {
        return "Snow Golem";
    }

    public function spawnToAll(): void
    {
        parent::spawnToAll();
        $this->setMaxHealth(100);
        $this->setNameTagAlwaysVisible(true);
        $this->setCanSaveWithChunk(true);
        $owner = PartnerItems::getInstance()->getServer()->getPlayerExact($this->owner);
        $this->setNameTag("§b§lPortable Faction Bard\n§bOwner§7: §f" . $owner->getName());
    }

    protected function initEntity(CompoundTag $nbt): void {
        $this->setMaxHealth(20);
        $this->setHealth(20);
        $this->setCanSaveWithChunk(false);
        parent::initEntity($nbt);
    }

    public function onUpdate(int $currentTick): bool
    {
        if($this->time === 0 || time() - $this->time >= 1) {
            $this->time = time();
            if ($this->owner == null) {
                $this->close();
                return parent::onUpdate($currentTick);
            }
            $owner = PartnerItems::getInstance()->getServer()->getPlayerExact($this->owner);
            $faction = FactionsAPI::getFaction($owner);
            foreach (FactionsAPI::getAllPlayers($faction) as $member) {
                if (Server::getInstance()->getPlayerExact($member)) {
                    if ($this->getPosition()->distance(Server::getInstance()->getPlayerExact($member)->getPosition()) <= 10) {
                        if (Server::getInstance()->getPlayerExact($member)->isOnline()) {
                            if(PortableFactionBard::isAllow($owner)) {
                                if ($this->count_down <= (50) && $this->count_down >= (35)) {
                                    Server::getInstance()->getPlayerExact($member)->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 2, 1));
                                    Server::getInstance()->getPlayerExact($member)->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 20 * 2, 1));
                                }
                                if ($this->count_down <= (35) && $this->count_down >= (20)) {
                                    Server::getInstance()->getPlayerExact($member)->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 2, 1));
                                    Server::getInstance()->getPlayerExact($member)->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 20 * 2, 2));
                                }
                                if ($this->count_down <= (20) && $this->count_down >= (10)) {
                                    Server::getInstance()->getPlayerExact($member)->getEffects()->add(new EffectInstance(VanillaEffects::INVISIBILITY(), 20 * 2, 0));
                                    Server::getInstance()->getPlayerExact($member)->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 2, 1));
                                }
                                if ($this->count_down <= (10) && $this->count_down >= (5)) {
                                    Server::getInstance()->getPlayerExact($member)->getEffects()->add(new EffectInstance(VanillaEffects::JUMP_BOOST(), 20 * 2, 7));
                                }
                                if ($this->count_down <= (5) && $this->count_down >= (0)) {
                                    Server::getInstance()->getPlayerExact($member)->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 2, 1));
                                    Server::getInstance()->getPlayerExact($member)->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 20 * 2, 1));
                                }
                            }
                        }
                    }
                }
            }
            if ($this->count_down > 0) {
                $this->count_down--;
            }
            if ($this->count_down <= 0) {
                $this->close();
            }
        }

        $this->teleport($this->pos);
        return parent::onUpdate($currentTick);
    }

    public function setOwner(Player $player)
    {
        $this->owner = $player->getName();
    }

    public function getXpDropAmount(): int {
        return 0;
    }

    public function getDrops(): array {
        return [VanillaItems::AIR()];
    }

    /**
     * @param Position $pos
     */
    public function setPos(Position $pos): void
    {
        $this->pos = $pos;
    }

    public function getOwner()
    {
        return $this->owner;
    }

}