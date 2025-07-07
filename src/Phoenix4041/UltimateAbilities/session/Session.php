<?php

declare(strict_types=1);

namespace Phoenix4041\UltimateAbilities\session;

use pocketmine\player\Player;

class Session
{
    private Player $player;
    private array $cooldowns = [];
    private array $effects = [];
    
    public function __construct(Player $player)
    {
        $this->player = $player;
    }
    
    public function getPlayer(): Player
    {
        return $this->player;
    }
    
    public function setCooldown(string $ability, int $seconds): void
    {
        $this->cooldowns[$ability] = time() + $seconds;
    }
    
    public function hasCooldown(string $ability): bool
    {
        return isset($this->cooldowns[$ability]) && $this->cooldowns[$ability] > time();
    }
    
    public function getCooldownTime(string $ability): int
    {
        if (!$this->hasCooldown($ability)) {
            return 0;
        }
        
        return $this->cooldowns[$ability] - time();
    }
    
    public function setEffect(string $effect, int $seconds): void
    {
        $this->effects[$effect] = time() + $seconds;
    }
    
    public function hasEffect(string $effect): bool
    {
        if (!isset($this->effects[$effect])) {
            return false;
        }
        
        if ($this->effects[$effect] <= time()) {
            unset($this->effects[$effect]);
            return false;
        }
        
        return true;
    }
    
    public function getEffectTime(string $effect): int
    {
        if (!$this->hasEffect($effect)) {
            return 0;
        }
        
        return $this->effects[$effect] - time();
    }
    
    public function removeEffect(string $effect): void
    {
        unset($this->effects[$effect]);
    }
    
    public function getEffects(): array
    {
        return $this->effects;
    }
    
    public function getCooldowns(): array
    {
        return $this->cooldowns;
    }
    
    public function clearExpiredCooldowns(): void
    {
        $currentTime = time();
        foreach ($this->cooldowns as $ability => $expireTime) {
            if ($expireTime <= $currentTime) {
                unset($this->cooldowns[$ability]);
            }
        }
    }
    
    public function clearExpiredEffects(): void
    {
        $currentTime = time();
        foreach ($this->effects as $effect => $expireTime) {
            if ($expireTime <= $currentTime) {
                unset($this->effects[$effect]);
            }
        }
    }
}