<?php
declare(strict_types=1);
namespace Phoenix4041\UltimateAbilities\entity;
use pocketmine\entity\projectile\Snowball;
use pocketmine\entity\Location;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\CompoundTag;

class SwitcherEntity extends Snowball {
    
    public static function getNetworkTypeId(): string {
        return "minecraft:snowball"; // Use string identifier instead of int
    }
    
    private bool $isSwitcherProjectile = true;
    
    public function __construct(Location $location, ?Entity $shootingEntity, ?CompoundTag $nbt = null) {
        parent::__construct($location, $shootingEntity, $nbt);
        $this->isSwitcherProjectile = true;
    }
    
    protected function initEntity(CompoundTag $nbt): void {
        parent::initEntity($nbt);
        $this->isSwitcherProjectile = $nbt->getByte("switcher_projectile", 1) === 1;
    }
    
    public function saveNBT(): CompoundTag {
        $nbt = parent::saveNBT();
        $nbt->setByte("switcher_projectile", $this->isSwitcherProjectile ? 1 : 0);
        return $nbt;
    }
    
    public function isSwitcherProjectile(): bool {
        return $this->isSwitcherProjectile;
    }
}