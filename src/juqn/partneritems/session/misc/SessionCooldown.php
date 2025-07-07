<?php

declare(strict_types=1);

namespace juqn\partneritems\session\misc;

class SessionCooldown
{
    
    /**
     * SessionCooldown construct.
     * @param string $name
     * @param int $time
     */
    public function __construct(
        private string $name,
        private int $time
    ) {}
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }
    
    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->time < time();
    }
}