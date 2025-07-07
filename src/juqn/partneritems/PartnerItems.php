<?php

declare(strict_types=1);

namespace juqn\partneritems;

use juqn\partneritems\command\PartnerItemsCommand;
use juqn\partneritems\item\Item;
use juqn\partneritems\item\ItemManager;
use juqn\partneritems\provider\Provider;
use juqn\partneritems\session\SessionManager;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use ReflectionException;

class PartnerItems extends PluginBase
{
    use SingletonTrait;
    
    /** @var ItemManager */
    private ItemManager $itemManager;
    /** @var SessionManager */
    private SessionManager $sessionManager;
    /** @var array */
    public static array $bard_allow = [];
    
    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    /**
     * @throws ReflectionException
     */
    protected function onEnable(): void
    {
        $this->saveResource("config.yml");
        # Register invmenu 
        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
        # Init provider
        Provider::init();
        # Fake enchantment
        EnchantmentIdMap::getInstance()->register(-1, new Enchantment('glow', 1, ItemFlags::ALL, ItemFlags::NONE, 1));
        # Register manager
        $this->itemManager = new ItemManager;
        $this->sessionManager = new SessionManager;
        # Events handler
        $this->getServer()->getPluginManager()->registerEvent(EntityDamageEvent::class, function (EntityDamageEvent $event): void {
            $player = $event->getEntity();
            
            if (!$player instanceof Player) {
                return;
            }
            $item = $player->getInventory()->getItemInHand();
            
            if ($item->getNamedTag()->getTag('partner_item') !== null) {
                $ability = $this->getItemManager()->getItem($item->getNamedTag()->getString('partner_item'));
                
                if ($ability !== null) {
                    $ability->damage($event);
                }
            }
        }, EventPriority::NORMAL, $this);
        $this->getServer()->getPluginManager()->registerEvent(PlayerItemUseEvent::class, function (PlayerItemUseEvent $event): void {
            $player = $event->getPlayer();
            $item = $event->getItem();
            $directionVector = $event->getDirectionVector();
            
            if ($item->getNamedTag()->getTag('partner_item') !== null) {
                $ability = $this->getItemManager()->getItem($item->getNamedTag()->getString('partner_item'));

                $ability?->onClickAir($player, $directionVector);
            }
        }, EventPriority::NORMAL, $this);
        $this->getServer()->getPluginManager()->registerEvent(PlayerLoginEvent::class, function (PlayerLoginEvent $event): void {
            $player = $event->getPlayer();
            $session = $this->getSessionManager()->getSession($player);
            
            if ($session === null) {
                $this->getSessionManager()->createSession($player);
            }
        }, EventPriority::NORMAL, $this);
        # Register command
        $this->getServer()->getCommandMap()->register('PartnerItems', new PartnerItemsCommand());
    }
    
    protected function onDisable(): void
    {
        Provider::save();
    }
    
    /**
     * @return ItemManager
     */
    public function getItemManager(): ItemManager
    {
        return $this->itemManager;
    }
    
    /**
     * @return SessionManager
     */
    public function getSessionManager(): SessionManager
    {
        return $this->sessionManager;
    }
}