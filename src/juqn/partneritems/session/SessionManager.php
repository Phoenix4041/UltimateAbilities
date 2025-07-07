<?php

declare(strict_types=1);

namespace juqn\partneritems\session;

use juqn\partneritems\provider\Provider;
use pocketmine\player\Player;

class SessionManager
{
    
    /**
     * SessionManager construct.
     * @param Session[] $sessions
     */
    public function __construct(
        private array $sessions = []
    ) {
        foreach (Provider::getPlayers() as $xuid => $data) {
            $session = new Session();
            
            foreach ($data['cooldowns'] as $name => $time) {
                if ($time > time()) {
                    $session->addCooldown($name, $time);
                }
            }
            $this->sessions[$xuid] = $session;
        }
    }
    
    /**
     * @return Session[]
     */
    public function getSessions(): array
    {
        return $this->sessions;
    }
    
    /**
     * @param Player $player
     * @return Session|null
     */
    public function getSession(Player $player): ?Session
    {
        return $this->sessions[$player->getXuid()] ?? null;
    }
    
    /**
     * @param Player $player
     */
    public function createSession(Player $player): void
    {
        $this->sessions[$player->getXuid()] = new Session();
    }
}