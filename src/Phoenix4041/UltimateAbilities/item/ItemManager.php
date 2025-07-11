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
    /** @var array */
    private array $abilityClasses = [];
    
    public function __construct()
    {
        $this->registerAbilityClasses();
    }
    
    private function registerAbilityClasses(): void
    {
        // En lugar de instanciar las habilidades, guardamos sus clases
        $this->abilityClasses = [
            'antipearl' => AntiPearl::class,
            'switcher' => Switcher::class,
            'ultrainstinct' => UltraInstinct::class,
            'teleportattack' => TeleportAttack::class,
            'strength' => Strength::class,
            'resistance' => Resistance::class,
            'redbull' => RedBull::class,
            'antitrapper' => AntiTrapper::class,
            'fenix' => Fenix::class,
            'megainstinct' => MegaInstinct::class,
            'heart' => Heart::class,
            'coffee' => Coffee::class,
            'alcohol' => Alcohol::class,
            'antigapple' => AntiGapple::class,
            'gemamente' => GemaMente::class,
            'gemapoder' => GemaPoder::class,
        ];
    }
    
    public function getAbility(string $name): ?AbilityItem
    {
        if (!isset($this->abilityClasses[$name])) {
            return null;
        }
        
        $className = $this->abilityClasses[$name];
        // Crear nueva instancia cada vez para obtener configuración actualizada
        return new $className();
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
        $abilities = [];
        foreach ($this->abilityClasses as $name => $class) {
            $abilities[$name] = new $class();
        }
        return $abilities;
    }
    
    public function getAbilityNames(): array
    {
        return array_keys($this->abilityClasses);
    }
    
    public function createAbilityItem(string $abilityName): ?\pocketmine\item\Item
    {
        $ability = $this->getAbility($abilityName);
        if ($ability === null) {
            return null;
        }
        
        return $ability->getItem();
    }
    
    /**
     * Reload all abilities (useful for config changes)
     */
    public function reloadAbilities(): void
    {
        // No necesitamos hacer nada especial aquí ya que las instancias
        // se crean dinámicamente y leerán la configuración actualizada
    }
}