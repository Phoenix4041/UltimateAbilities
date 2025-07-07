<?php

declare(strict_types=1);

namespace Phoenix4041\UltimateAbilities;

use Phoenix4041\UltimateAbilities\command\UltimateAbilitiesCommand;
use Phoenix4041\UltimateAbilities\item\AbilityItem;
use Phoenix4041\UltimateAbilities\item\ItemManager;
use Phoenix4041\UltimateAbilities\session\SessionManager;
use Phoenix4041\UltimateAbilities\provider\Provider;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use ReflectionException;

class UltimateAbilities extends PluginBase
{
    use SingletonTrait;
    
    private ItemManager $itemManager;
    private SessionManager $sessionManager;
    
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
        
        # Fake enchantment for glow effect
        EnchantmentIdMap::getInstance()->register(-1, new Enchantment('glow', 1, ItemFlags::ALL, ItemFlags::NONE, 1));
        
        # Register managers
        $this->itemManager = new ItemManager();
        $this->sessionManager = new SessionManager();
        
        # Register events
        $this->registerEvents();
        
        # Register command
        $this->getServer()->getCommandMap()->register('UltimateAbilities', new UltimateAbilitiesCommand());
        
        $this->getLogger()->info("§aUltimateAbilities has been enabled!");
    }
    
    protected function onDisable(): void
    {
        Provider::save();
        $this->getLogger()->info("§cUltimateAbilities has been disabled!");
    }
    
    private function registerEvents(): void
    {
        # Player item use event
        $this->getServer()->getPluginManager()->registerEvent(PlayerItemUseEvent::class, function (PlayerItemUseEvent $event): void {
            $player = $event->getPlayer();
            $item = $event->getItem();
            
            if ($item->getNamedTag()->getTag('ultimate_ability') !== null) {
                $abilityName = $item->getNamedTag()->getString('ultimate_ability');
                $ability = $this->itemManager->getAbility($abilityName);
                
                if ($ability !== null) {
                    $ability->onUse($player, $event->getDirectionVector());
                }
            }
        }, EventPriority::NORMAL, $this);
        
        # Player interact event
        $this->getServer()->getPluginManager()->registerEvent(PlayerInteractEvent::class, function (PlayerInteractEvent $event): void {
            $player = $event->getPlayer();
            $item = $event->getItem();
            
            if ($item->getNamedTag()->getTag('ultimate_ability') !== null) {
                $abilityName = $item->getNamedTag()->getString('ultimate_ability');
                $ability = $this->itemManager->getAbility($abilityName);
                
                if ($ability !== null) {
                    $ability->onInteract($player, $event->getAction(), $event->getBlock());
                }
            }
        }, EventPriority::NORMAL, $this);
        
        # Entity damage by entity event
        $this->getServer()->getPluginManager()->registerEvent(EntityDamageByEntityEvent::class, function (EntityDamageByEntityEvent $event): void {
            $damager = $event->getDamager();
            $victim = $event->getEntity();
            
            if (!$damager instanceof Player) {
                return;
            }
            
            $item = $damager->getInventory()->getItemInHand();
            
            if ($item->getNamedTag()->getTag('ultimate_ability') !== null) {
                $abilityName = $item->getNamedTag()->getString('ultimate_ability');
                $ability = $this->itemManager->getAbility($abilityName);
                
                if ($ability !== null) {
                    $ability->onAttack($damager, $victim, $event);
                }
            }
        }, EventPriority::NORMAL, $this);
        
        # Player login event
        $this->getServer()->getPluginManager()->registerEvent(PlayerLoginEvent::class, function (PlayerLoginEvent $event): void {
            $player = $event->getPlayer();
            $session = $this->sessionManager->getSession($player);
            
            if ($session === null) {
                $this->sessionManager->createSession($player);
            }
        }, EventPriority::NORMAL, $this);
    }
    
    public function getItemManager(): ItemManager
    {
        return $this->itemManager;
    }
    
    public function getSessionManager(): SessionManager
    {
        return $this->sessionManager;
    }
}