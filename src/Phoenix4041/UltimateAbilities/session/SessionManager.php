<?php

namespace Phoenix4041\UltimateAbilities\session;

use pocketmine\player\Player;

class SessionManager
{
    private array $sessions = [];

    public function createSession(Player $player): Session
    {
        $session = new Session($player);
        $this->sessions[$player->getName()] = $session;
        return $session;
    }

    public function getSession(Player $player): ?Session
    {
        return $this->sessions[$player->getName()] ?? null;
    }

    /**
     * Limpiar todos los cooldowns de todas las sesiones
     */
    public function clearAllCooldowns(): void
    {
        foreach ($this->sessions as $session) {
            $session->clearAllCooldowns(); // Assuming Session class has this method
        }
    }

    public function removeSession(Player $player): void
    {
        unset($this->sessions[$player->getName()]);
    }

    public function getAllSessions(): array
    {
        return $this->sessions;
    }

    public function updateCooldowns(): void
    {
        foreach ($this->sessions as $session) {
            $session->updateCooldowns();
        }
    }
}