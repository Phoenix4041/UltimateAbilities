<?php
declare(strict_types=1);
namespace Phoenix4041\UltimateAbilities\listener;
use Phoenix4041\UltimateAbilities\UltimateAbilities;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
class AntiTrapperListener implements Listener
{
    private UltimateAbilities $plugin;
    
    public function __construct(UltimateAbilities $plugin)
    {
        $this->plugin = $plugin;
    }
    
    public function onBlockBreak(BlockBreakEvent $event): void
    {
        $player = $event->getPlayer();
        $blockPos = $event->getBlock()->getPosition();
        
        if ($this->isBlockedByAntiTrapper($player, $blockPos)) {
            $event->cancel();
            $player->sendMessage("§c¡No puedes romper bloques! Hay un Anti Trapper activo cerca.");
        }
    }
    
    public function onBlockPlace(BlockPlaceEvent $event): void
    {
        $player = $event->getPlayer();
        // Fix: Use getBlockAgainst() or getBlockClicked() instead of getBlock()
        $blockPos = $event->getBlockAgainst()->getPosition();
        
        if ($this->isBlockedByAntiTrapper($player, $blockPos)) {
            $event->cancel();
            $player->sendMessage("§c¡No puedes poner bloques! Hay un Anti Trapper activo cerca.");
        }
    }
    
    private function isBlockedByAntiTrapper(Player $player, Vector3 $blockPos): bool
    {
        $world = $player->getWorld();
        
        // Verificar todos los jugadores en el mundo
        foreach ($world->getPlayers() as $otherPlayer) {
            if ($otherPlayer === $player) continue;
            
            $session = $this->plugin->getSessionManager()->getSession($otherPlayer);
            if ($session === null) continue;
            
            // Verificar si el otro jugador tiene el efecto antitrapper activo
            if ($session->hasEffect('antitrapper')) {
                $antitrapperPos = $otherPlayer->getPosition();
                $radius = 10; // Radio fijo de 10 bloques
                
                $distance = $blockPos->distance($antitrapperPos);
                
                // Si el bloque está dentro del radio del antitrapper
                if ($distance <= $radius) {
                    return true;
                }
            }
        }
        
        return false;
    }
}