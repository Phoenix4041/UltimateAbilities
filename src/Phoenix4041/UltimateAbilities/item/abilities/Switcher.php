<?php
declare(strict_types=1);
namespace Phoenix4041\UltimateAbilities\item\abilities;
use Phoenix4041\UltimateAbilities\entity\SwitcherEntity;
use Phoenix4041\UltimateAbilities\item\AbilityItem;
use Phoenix4041\UltimateAbilities\provider\Provider;
use pocketmine\entity\Location;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\Listener;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\particle\EndermanTeleportParticle;
use pocketmine\world\sound\EndermanTeleportSound;
use pocketmine\event\EventPriority;

class Switcher extends AbilityItem implements Listener
{
    public function __construct()
    {
        $config = Provider::getAbilityConfig('switcher');
        parent::__construct(
            $config['name'] ?? "§b§lSwitcher",
            VanillaItems::SNOWBALL(),
            $config['cooldown'] ?? 25,
            $config['lore'] ?? [
                "§7Intercambia posiciones con",
                "§7el jugador que golpees",
                "",
                "§aLanza la bola de nieve para usar"
            ]
        );
    }
    
    public function getAbilityName(): string
    {
        return "switcher";
    }
    
    protected function execute(Player $player, Vector3 $directionVector): void
    {
        $world = $player->getWorld();
        $location = $player->getLocation();
        
        // USAR SwitcherEntity en lugar de Snowball normal
        $switcherEntity = new SwitcherEntity($location, $player);
        $switcherEntity->setMotion($directionVector->multiply(1.5));
        $switcherEntity->spawnToAll();
        
        $this->sendMessage($player, "§b¡Switcher lanzado!");
    }
    
    /**
     * @param ProjectileHitEntityEvent $event
     * @priority NORMAL
     * @ignoreCancelled true
     */
    public function onHitByProjectile(ProjectileHitEntityEvent $event): void 
    {
        $entity = $event->getEntity();
        $hit = $event->getEntityHit();
        
        // Solo procesar si es SwitcherEntity
        if (!$entity instanceof SwitcherEntity) {
            return;
        }
        
        $player = $entity->getOwningEntity();
        if (!$player instanceof Player || !$player->isOnline()) {
            return;
        }
        
        if (!$hit instanceof Player) {
            $this->sendMessage($player, "§cSolo puedes intercambiar con otros jugadores");
            return;
        }
        
        // No permitir intercambio consigo mismo
        if ($player === $hit) {
            $this->sendMessage($player, "§cNo puedes intercambiar contigo mismo");
            return;
        }
        
        // Verificar que el jugador objetivo esté en línea
        if (!$hit->isOnline()) {
            $this->sendMessage($player, "§cEl jugador objetivo no está en línea");
            return;
        }
        
        // Obtener posiciones
        $damagerPos = $player->getPosition();
        $victimPos = $hit->getPosition();
        
        // Efectos visuales y sonoros antes del intercambio
        $player->getWorld()->addParticle($damagerPos, new EndermanTeleportParticle());
        $hit->getWorld()->addParticle($victimPos, new EndermanTeleportParticle());
        
        $player->getWorld()->addSound($damagerPos, new EndermanTeleportSound());
        $hit->getWorld()->addSound($victimPos, new EndermanTeleportSound());
        
        // Intercambiar posiciones
        $player->teleport($victimPos);
        $hit->teleport($damagerPos);
        
        // Efectos después del intercambio
        $player->getWorld()->addParticle($player->getPosition(), new EndermanTeleportParticle());
        $hit->getWorld()->addParticle($hit->getPosition(), new EndermanTeleportParticle());
        
        // Mensajes de confirmación
        $this->sendMessage($player, "§b¡Has intercambiado posiciones con §e{$hit->getName()}§b!");
        $this->sendMessage($hit, "§b¡§e{$player->getName()} §bha intercambiado posiciones contigo!");
        
        // Despawn del proyectil
        $entity->flagForDespawn();
    }
}