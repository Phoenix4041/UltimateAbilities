<?php

declare(strict_types=1);

namespace juqn\partneritems\item;

use juqn\partneritems\entity\PortableBardEntity;
use juqn\partneritems\entity\PortableFactionBardEntity;
use juqn\partneritems\entity\SwitcherEntity;
use juqn\partneritems\item\defaults\Aids;
use juqn\partneritems\item\defaults\Alcohol;
use juqn\partneritems\item\defaults\CheemStick;
use juqn\partneritems\item\defaults\Cigar;
use juqn\partneritems\item\defaults\Coffee;
use juqn\partneritems\item\defaults\DisableEffects;
use juqn\partneritems\item\defaults\Drug;
use juqn\partneritems\item\defaults\Heart;
use juqn\partneritems\item\defaults\MegaInstinct;
use juqn\partneritems\item\defaults\Mota;
use juqn\partneritems\item\defaults\Phoenix;
use juqn\partneritems\item\defaults\PortableBard;
use juqn\partneritems\item\defaults\PortableFactionBard;
use juqn\partneritems\item\defaults\Pots;
use juqn\partneritems\item\defaults\Redbull;
use juqn\partneritems\item\defaults\Resistance;
use juqn\partneritems\item\defaults\StormBreaker;
use juqn\partneritems\item\defaults\Strength;
use juqn\partneritems\item\defaults\Switcher;
use juqn\partneritems\item\defaults\TeleportAttack;
use juqn\partneritems\item\defaults\UltraInstinct;
use juqn\partneritems\item\defaults\Weakness;
use juqn\partneritems\PartnerItems;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\world\World;

class ItemManager
{
    
    /**
     * ItemManager construct.
     */
    public function __construct(
        private array $items = []
    ) {
        $this->registerItem(
            new Aids,
            new Alcohol,
            new CheemStick,
            new Cigar,
            new Coffee,
            new DisableEffects,
            new Drug,
            new Heart,
            new MegaInstinct,
            new Mota,
            new Phoenix,
            new Pots,
            new Redbull,
            new Resistance,
            new StormBreaker,
            new Strength,
            new TeleportAttack,
            new UltraInstinct,
            new Weakness,
            new PortableFactionBard(),
            new PortableBard(),
            new Switcher
        );

        PartnerItems::getInstance()->getServer()->getPluginManager()->registerEvents(new Switcher(), PartnerItems::getInstance());

        EntityFactory::getInstance()->register(PortableFactionBardEntity::class, function(World $world, CompoundTag $nbt) : PortableFactionBardEntity{
            return new PortableFactionBardEntity(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ['PortableFactionBardEntity', 'minecraft:snow_golem'], EntityIds::SNOW_GOLEM);

        EntityFactory::getInstance()->register(SwitcherEntity::class, function (World $world, CompoundTag $nbt): SwitcherEntity {
            return new SwitcherEntity(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        }, ['SwitcherEntity']);

        EntityFactory::getInstance()->register(PortableBardEntity::class, function(World $world, CompoundTag $nbt): PortableBardEntity{
            return new PortableBardEntity(EntityDataHelper::parseLocation($nbt,$world));
        },["PortableBardEntity"]);

    }
    
    /**
     * @return Item[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
    
    /**
     * @param string $name
     * @return Item|null
     */
    public function getItem(string $name): ?Item
    {
        return $this->items[$name] ?? null;
    }

    public function registerItem(Item ...$items): void
    {
        foreach ($items as $item) {
            $this->items[$item->getName()] = $item;
        }
    }
}