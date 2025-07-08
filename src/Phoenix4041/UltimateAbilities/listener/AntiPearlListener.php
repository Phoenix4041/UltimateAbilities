<?php
declare(strict_types=1);

namespace Phoenix4041\UltimateAbilities\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\EnderPearl;
use pocketmine\player\Player;
use pocketmine\world\sound\NoteSound;
use pocketmine\world\sound\NoteInstrument;
use Phoenix4041\UltimateAbilities\UltimateAbilities;

class AntiPearlListener implements Listener
{
    private UltimateAbilities $plugin;

    public function __construct(UltimateAbilities $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onPlayerItemUse(PlayerItemUseEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();

        // Check if the item is an ender pearl
        if (!$item instanceof EnderPearl) {
            return;
        }

        // Get the player's session
        $session = $this->plugin->getSessionManager()->getSession($player);
        if ($session === null) {
            return;
        }

        // Check if the player has the antipearl effect
        if ($session->hasEffect('antipearl')) {
            $event->cancel();
            $remainingTime = $session->getEffectTime('antipearl');
            $player->sendMessage("§c¡No puedes usar ender pearls! Tiempo restante: §e{$remainingTime}s");
            
            // Play a sound effect to indicate the block
            $player->getWorld()->addSound($player->getPosition(), new NoteSound(NoteInstrument::BASS_DRUM(), 0.5));
        }
    }
}