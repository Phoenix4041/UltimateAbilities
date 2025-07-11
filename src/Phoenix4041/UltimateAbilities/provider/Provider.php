<?php

declare(strict_types=1);

namespace Phoenix4041\UltimateAbilities\provider;

use Phoenix4041\UltimateAbilities\UltimateAbilities;
use pocketmine\utils\Config;

class Provider
{
    private static ?Config $config = null;
    
    /**
     * Get the configuration, always fresh from file
     */
    private static function getConfig(): Config
    {
        // Siempre recargar la configuración desde el archivo
        $plugin = UltimateAbilities::getInstance();
        
        // Recargar la configuración del plugin
        $plugin->reloadConfig();
        
        // Crear nueva instancia del Config
        return new Config($plugin->getDataFolder() . "config.yml", Config::YAML);
    }
    
    /**
     * Get ability configuration by name
     */
    public static function getAbilityConfig(string $abilityName): array
    {
        $config = self::getConfig();
        
        // Debug: Log para ver qué se está leyendo del archivo
        $plugin = UltimateAbilities::getInstance();
        $plugin->getLogger()->info("DEBUG - Reading config for ability: " . $abilityName);
        
        $abilityConfig = $config->get($abilityName, []);
        
        // Debug: Log del valor específico del cooldown
        $plugin->getLogger()->info("DEBUG - Raw config data: " . json_encode($abilityConfig));
        
        return $abilityConfig;
    }
    
    /**
     * Get all abilities configuration
     */
    public static function getAllAbilitiesConfig(): array
    {
        $config = self::getConfig();
        return $config->getAll();
    }
    
    /**
     * Force reload configuration
     */
    public static function reloadConfig(): void
    {
        self::$config = null; // Clear any cached config
        
        $plugin = UltimateAbilities::getInstance();
        $plugin->reloadConfig();
        
        $plugin->getLogger()->info("Configuration reloaded!");
    }
}