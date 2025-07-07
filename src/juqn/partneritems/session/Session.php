<?php

declare(strict_types=1);

namespace juqn\partneritems\session;

use juqn\partneritems\session\misc\SessionCooldown;

class Session
{
    
    /**
     * Session construct.
     * @param SessionCooldown[] $cooldowns
     */
    public function __construct(
        private array $cooldowns = []
    ) {}
    
    /**
     * @param string $name
     * @return SessionCooldown|null
     */
    public function getCooldown(string $name): ?SessionCooldown
    {
        return $this->cooldowns[$name] ?? null;
    }
    
    /**
     * @param string $name
     * @param int $time
     * @param bool $exact
     */
    public function addCooldown(string $name, int $time, bool $exact = false): void
    {
        $this->cooldowns[$name] = new SessionCooldown($name, $exact ? $time : time() + $time);
    }
    
    /**
     * @return array
     */
    public function serializeData(): array
    {
        $data = [
            'cooldowns' => []
        ];
        
        foreach ($this->cooldowns as $cooldown) {
            $data['cooldowns'][$cooldown->getName()] = $cooldown->getTime();
        }
        return $data;
    }
}