<?php
declare(strict_types=1);

namespace Phoenix4041\UltimateAbilities\item\abilities;

use Phoenix4041\UltimateAbilities\item\AbilityItem;
use Phoenix4041\UltimateAbilities\provider\Provider;
use Phoenix4041\UltimateAbilities\UltimateAbilities;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\world\particle\FlameParticle;
use pocketmine\world\sound\NoteInstrument;
use pocketmine\world\sound\NoteSound;

class AntiPearl extends AbilityItem
{
    public function __construct()
    {
        $config = Provider::getAbilityConfig('antipearl');
        parent::__construct(
            $config['name'] ?? "§c§lAnti Pearl",
            VanillaItems::SHEARS(),
            $config['cooldown'] ?? 30,
            $config['lore'] ?? [
                "§7Bloquea el uso de ender pearls",
                "§7en un área durante 10 segundos",
                "",
                "§cAtaca a un jugador para usar"
            ]
        );
    }
    
    public function getAbilityName(): string
    {
        return "antipearl";
    }
    
    protected function execute(Player $player, Vector3 $directionVector): void
    {
        // This ability is triggered by attacking, not by using the item directly
        $this->sendMessage($player, "§c¡Debes atacar a un jugador para usar esta habilidad!");
    }
    
    public function onAttack(Player $damager, Entity $victim, EntityDamageByEntityEvent $event): void
    {
        if (!($victim instanceof Player)) {
            return;
        }
        
        // Check if the damager is holding the Anti Pearl item
        $item = $damager->getInventory()->getItemInHand();
        if (!$this->isSameItem($item)) {
            return;
        }
        
        if ($this->isOnCooldown($damager)) {
            $this->sendMessage($damager, "§c¡Debes esperar " . $this->getRemainingCooldown($damager) . " segundos!");
            $event->cancel();
            return;
        }
        
        // Cancel the attack to prevent normal damage and item consumption
        $event->cancel();
        
        $this->setCooldown($damager);
        
        // Get the session manager instance
        $plugin = UltimateAbilities::getInstance();
        $sessionManager = $plugin->getSessionManager();
        
        $targetSession = $sessionManager->getSession($victim);
        if ($targetSession !== null) {
            $targetSession->setEffect('antipearl', 10);
        }
        
        $world = $victim->getWorld();
        $pos = $victim->getPosition();
        
        // Create anti-pearl zone with visual effects
        for ($i = 0; $i < 20; $i++) {
            $x = $pos->x + mt_rand(-5, 5);
            $y = $pos->y + mt_rand(0, 3);
            $z = $pos->z + mt_rand(-5, 5);
            $world->addParticle(new Vector3($x, $y, $z), new FlameParticle());
        }
        
        $world->addSound($pos, new NoteSound(NoteInstrument::BASS_DRUM(), 1));
        
        $this->sendMessage($damager, "§c¡Zona Anti-Pearl activada en " . $victim->getName() . " durante 10 segundos!");
        $victim->sendMessage("§c¡{$damager->getName()} te ha aplicado Anti Pearl! No puedes usar ender pearls por 10 segundos!");
        
        // Notify nearby players
        foreach ($world->getNearbyEntities($victim->getBoundingBox()->expandedCopy(10, 10, 10)) as $entity) {
            if ($entity instanceof Player && $entity !== $damager && $entity !== $victim) {
                $entity->sendMessage("§c¡{$damager->getName()} ha activado una zona Anti-Pearl en " . $victim->getName() . "!");
            }
        }
    }
    
    /**
     * Check if the given item is the same as this ability item
     */
    private function isSameItem(\pocketmine\item\Item $item): bool
    {
        return $item->getTypeId() === $this->getItem()->getTypeId() && 
               $item->hasCustomName() && 
               $item->getCustomName() === $this->getItem()->getCustomName();
    }
}