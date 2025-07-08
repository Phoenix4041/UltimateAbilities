<?php

declare(strict_types=1);

namespace Phoenix4041\UltimateAbilities\item;

use Phoenix4041\UltimateAbilities\item\abilities\AntiPearl;
use Phoenix4041\UltimateAbilities\item\abilities\Switcher;
use Phoenix4041\UltimateAbilities\item\abilities\UltraInstinct;
use Phoenix4041\UltimateAbilities\item\abilities\TeleportAttack;
use Phoenix4041\UltimateAbilities\item\abilities\Strength;
use Phoenix4041\UltimateAbilities\item\abilities\Resistance;
use Phoenix4041\UltimateAbilities\item\abilities\RedBull;
use Phoenix4041\UltimateAbilities\item\abilities\AntiTrapper;
use Phoenix4041\UltimateAbilities\item\abilities\Fenix;
use Phoenix4041\UltimateAbilities\item\abilities\MegaInstinct;
use Phoenix4041\UltimateAbilities\item\abilities\Heart;
use Phoenix4041\UltimateAbilities\item\abilities\Coffee;
use Phoenix4041\UltimateAbilities\item\abilities\Alcohol;
use Phoenix4041\UltimateAbilities\item\abilities\AntiGapple;
use Phoenix4041\UltimateAbilities\item\abilities\GemaMente;
use Phoenix4041\UltimateAbilities\item\abilities\GemaPoder;

class ItemManager
{
    /** @var AbilityItem[] */
    private array $abilities = [];
    
    public function __construct()
    {
        $this->registerAbilities();
    }
    
    private function registerAbilities(): void
    {
        $this->abilities['antipearl'] = new AntiPearl();
        $this->abilities['switcher'] = new Switcher();
        $this->abilities['ultrainstinct'] = new UltraInstinct();
        $this->abilities['teleportattack'] = new TeleportAttack();
        $this->abilities['strength'] = new Strength();
        $this->abilities['resistance'] = new Resistance();
        $this->abilities['redbull'] = new RedBull();
        $this->abilities['antitrapper'] = new AntiTrapper();
        $this->abilities['fenix'] = new Fenix();
        $this->abilities['megainstinct'] = new MegaInstinct();
        $this->abilities['heart'] = new Heart();
        $this->abilities['coffee'] = new Coffee();
        $this->abilities['alcohol'] = new Alcohol();
        $this->abilities['antigapple'] = new AntiGapple();
        $this->abilities['gemamente'] = new GemaMente();
        $this->abilities['gemapoder'] = new GemaPoder();
    }
    
    public function getAbility(string $name): ?AbilityItem
    {
        return $this->abilities[$name] ?? null;
    }
    
    /**
     * Get ability item (Item object) by name
     * This is the method your command is calling
     */
    public function getAbilityItem(string $name): ?\pocketmine\item\Item
    {
        $ability = $this->getAbility($name);
        if ($ability === null) {
            return null;
        }
        
        return $ability->getItem();
    }
    
    public function getAllAbilities(): array
    {
        return $this->abilities;
    }
    
    public function getAbilityNames(): array
    {
        return array_keys($this->abilities);
    }
    
    public function createAbilityItem(string $abilityName): ?\pocketmine\item\Item
    {
        $ability = $this->getAbility($abilityName);
        if ($ability === null) {
            return null;
        }
        
        return $ability->getItem();
    }
}