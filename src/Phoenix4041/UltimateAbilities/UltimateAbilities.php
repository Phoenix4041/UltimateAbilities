<?php

declare(strict_types=1);

namespace Phoenix4041\UltimateAbilities;

use Phoenix4041\UltimateAbilities\command\UltimateAbilitiesCommand;
use Phoenix4041\UltimateAbilities\command\ReloadCommand;
use Phoenix4041\UltimateAbilities\item\AbilityItem;
use Phoenix4041\UltimateAbilities\item\ItemManager;
use Phoenix4041\UltimateAbilities\session\SessionManager;
use Phoenix4041\UltimateAbilities\provider\Provider;
use Phoenix4041\UltimateAbilities\listener\AntiPearlListener;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\Listener;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\VanillaItems;
use pocketmine\item\EnderPearl;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use ReflectionException;
use Phoenix4041\UltimateAbilities\listener\AntiTrapperListener;
use Phoenix4041\UltimateAbilities\listener\AntiGappleListener;
use Phoenix4041\UltimateAbilities\listener\AbilityListener;
use Phoenix4041\UltimateAbilities\entity\SwitcherEntity;

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
        
        # Register custom entities
        $this->registerEntities();
        
        # Register managers
        $this->itemManager = new ItemManager();
        $this->sessionManager = new SessionManager();
        
        # Register events
        $this->registerEvents();
        
        # Register commands
        $this->getServer()->getCommandMap()->register('UltimateAbilities', new UltimateAbilitiesCommand());
        $this->getServer()->getCommandMap()->register('ultimateabilities', new ReloadCommand($this));
        
        # Start cooldown updater task
        $this->getScheduler()->scheduleRepeatingTask(new \pocketmine\scheduler\ClosureTask(function(): void {
            $this->sessionManager->updateCooldowns();
        }), 20); // Run every second
        
        $this->getLogger()->info("§aUltimateAbilities has been enabled!");
    }
    
    protected function onDisable(): void
    {
        Provider::save();
        $this->getLogger()->info("§cUltimateAbilities has been disabled!");
    }
    
    private function registerEntities(): void
    {
        # Register SwitcherEntity
        \pocketmine\entity\EntityFactory::getInstance()->register(SwitcherEntity::class, function(\pocketmine\world\World $world, \pocketmine\nbt\tag\CompoundTag $nbt): SwitcherEntity {
            return new SwitcherEntity(\pocketmine\entity\EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        }, ['SwitcherEntity']);
    }
    
    private function registerEvents(): void
    {
        # Player item use event - MODIFICADO PARA CONSUMIR EL ÍTEM
        $this->getServer()->getPluginManager()->registerEvent(PlayerItemUseEvent::class, function (PlayerItemUseEvent $event): void {
            $player = $event->getPlayer();
            $item = $event->getItem();
            
            // Check for AntiPearl effect on ender pearl use
            if ($item instanceof EnderPearl) {
                $session = $this->sessionManager->getSession($player);
                if ($session !== null && $session->hasEffect('antipearl')) {
                    $event->cancel();
                    $player->sendMessage("§c¡No puedes usar ender pearls! Estás bajo el efecto Anti-Pearl!");

                    return;
                }
            }
            
            if ($item->getNamedTag()->getTag('ultimate_ability') !== null) {
                $abilityName = $item->getNamedTag()->getString('ultimate_ability');
                $ability = $this->itemManager->getAbility($abilityName);
                
                if ($ability !== null) {
                    $session = $this->sessionManager->getSession($player);
                    
                    // Verificar cooldown
                    if ($session !== null && $session->hasCooldown($abilityName)) {
                        $remaining = $session->getRemainingCooldown($abilityName);
                        $player->sendMessage("§cDebes esperar {$remaining} segundos para usar esta habilidad!");
                        return;
                    }
                    
                    // Usar la habilidad
                    $ability->onUse($player, $event->getDirectionVector());
                    
                    // CONSUMIR EL ÍTEM - Esta es la parte nueva
                    $this->consumeItem($player);
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
        
        # Projectile hit events - CORREGIDO PARA PM5
        $this->getServer()->getPluginManager()->registerEvent(ProjectileHitBlockEvent::class, function (ProjectileHitBlockEvent $event): void {
            $projectile = $event->getEntity();
            $owner = $projectile->getOwningEntity();
            
            if (!$owner instanceof Player) {
                return;
            }
            
            // Verificar si es un proyectil de switcher usando NBT - CORREGIDO PARA PM5
            $nbt = $projectile->saveNBT();
            if ($nbt->getTag('switcher_projectile') === null) {
                return;
            }
            
            $hitBlock = $event->getBlockHit();
            $hitPos = $hitBlock->getPosition();
            
            // Verificar cooldown del switcher
            $session = $this->sessionManager->getSession($owner);
            if ($session !== null && $session->hasCooldown('switcher')) {
                $remaining = $session->getRemainingCooldown('switcher');
                $owner->sendMessage("§cDebes esperar §e{$remaining}s §cpara usar Switcher.");
                return;
            }
            
            // Teletransportar al jugador a la posición del bloque golpeado
            $teleportPos = $hitPos->add(0, 1, 0);
            $owner->teleport($teleportPos);
            
            // Efectos visuales
            if (class_exists('pocketmine\world\particle\EndermanTeleportParticle')) {
                $owner->getWorld()->addParticle($owner->getPosition(), new \pocketmine\world\particle\EndermanTeleportParticle());
            }
            
            // Reproducir sonido si está disponible
            if (class_exists('pocketmine\world\sound\EndermanTeleportSound')) {
                $owner->getWorld()->addSound($owner->getPosition(), new \pocketmine\world\sound\EndermanTeleportSound());
            }
            
            // Aplicar cooldown
            if ($session !== null) {
                $session->setCooldown('switcher', 25);
            }
            
            $owner->sendMessage("§b¡Te has teletransportado al bloque!");
            
            // Remover el proyectil
            $projectile->flagForDespawn();
        }, EventPriority::NORMAL, $this);
        
        $this->getServer()->getPluginManager()->registerEvent(ProjectileHitEntityEvent::class, function (ProjectileHitEntityEvent $event): void {
            $projectile = $event->getEntity();
            $owner = $projectile->getOwningEntity();
            $hitEntity = $event->getEntityHit();
            
            if (!$owner instanceof Player) {
                return;
            }
            
            // Verificar si es un proyectil de switcher usando NBT - CORREGIDO PARA PM5
            $nbt = $projectile->saveNBT();
            if ($nbt->getTag('switcher_projectile') === null) {
                return;
            }
            
            if ($hitEntity instanceof Player) {
                // Verificar cooldown del switcher
                $session = $this->sessionManager->getSession($owner);
                if ($session !== null && $session->hasCooldown('switcher')) {
                    $remaining = $session->getRemainingCooldown('switcher');
                    $owner->sendMessage("§cDebes esperar §e{$remaining}s §cpara usar Switcher.");
                    return;
                }
                
                // Intercambiar posiciones entre el dueño y el jugador golpeado
                $ownerPos = $owner->getPosition();
                $hitEntityPos = $hitEntity->getPosition();
                
                // Efectos visuales antes del intercambio
                if (class_exists('pocketmine\world\particle\EndermanTeleportParticle')) {
                    $owner->getWorld()->addParticle($ownerPos, new \pocketmine\world\particle\EndermanTeleportParticle());
                    $hitEntity->getWorld()->addParticle($hitEntityPos, new \pocketmine\world\particle\EndermanTeleportParticle());
                }
                
                $owner->teleport($hitEntityPos);
                $hitEntity->teleport($ownerPos);
                
                // Efectos visuales después del intercambio
                if (class_exists('pocketmine\world\particle\EndermanTeleportParticle')) {
                    $owner->getWorld()->addParticle($owner->getPosition(), new \pocketmine\world\particle\EndermanTeleportParticle());
                    $hitEntity->getWorld()->addParticle($hitEntity->getPosition(), new \pocketmine\world\particle\EndermanTeleportParticle());
                }
                
                // Reproducir sonidos si está disponible
                if (class_exists('pocketmine\world\sound\EndermanTeleportSound')) {
                    $owner->getWorld()->addSound($ownerPos, new \pocketmine\world\sound\EndermanTeleportSound());
                    $hitEntity->getWorld()->addSound($hitEntityPos, new \pocketmine\world\sound\EndermanTeleportSound());
                }
                
                // Aplicar cooldown
                if ($session !== null) {
                    $session->setCooldown('switcher', 25);
                }
                
                $owner->sendMessage("§b¡Has intercambiado posiciones con §e{$hitEntity->getName()}§b!");
                $hitEntity->sendMessage("§b¡§e{$owner->getName()} §bha intercambiado posiciones contigo!");
            }
            
            // Remover el proyectil
            $projectile->flagForDespawn();
        }, EventPriority::NORMAL, $this);
        
        # Register listeners
        $this->getServer()->getPluginManager()->registerEvents(new AntiTrapperListener($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new AntiGappleListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new AntiPearlListener($this), $this);
    }
    
    /**
     * clear if u consume
     */
    private function consumeItem(Player $player): void
    {
        $inventory = $player->getInventory();
        $item = $inventory->getItemInHand();
        
        if ($item->getCount() > 1) {
            // Si hay más de 1, reducir la cantidad
            $item->setCount($item->getCount() - 1);
            $inventory->setItemInHand($item);
        } else {
            // Si solo hay 1, remover completamente
            $inventory->setItemInHand(VanillaItems::AIR());
        }
    }
    
    /**
     * Limpiar todos los cooldowns de todas las sesiones
     */
    public function clearAllCooldowns(): void
    {
        $this->sessionManager->clearAllCooldowns();
        $this->getLogger()->info("Todos los cooldowns han sido limpiados");
    }
    
    /**
     * Recargar configuraciones personalizadas
     */
    public function reloadConfigs(): void
    {
        $this->reloadConfig();
        Provider::reload();
        $this->getLogger()->info("Configuraciones recargadas");
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