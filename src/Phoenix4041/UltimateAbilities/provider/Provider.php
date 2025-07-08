<?php

declare(strict_types=1);

namespace Phoenix4041\UltimateAbilities\provider;

use Phoenix4041\UltimateAbilities\UltimateAbilities;
use pocketmine\utils\Config;

class Provider
{
    private static ?Config $config = null;
    private static array $abilityConfigs = [];
    
    public static function init(): void
    {
        self::$config = new Config(UltimateAbilities::getInstance()->getDataFolder() . "config.yml", Config::YAML);
    }
    
    public static function getConfig(): Config
    {
        if (self::$config === null) {
            self::init();
        }
        return self::$config;
    }
    
    public static function save(): void
    {
        if (self::$config !== null) {
            self::$config->save();
        }
    }
    
    /**
     * Recargar todas las configuraciones
     */
    public static function reload(): void
    {
        self::$config = null;
        self::$abilityConfigs = [];
        
        // Forzar recarga de configuraciones
        self::getConfig();
        
        // Pre-cargar configuraciones de habilidades comunes
        $commonAbilities = ['switcher', 'fireball', 'lightning', 'teleport', 'grappling', 'ninja', 'rocket'];
        foreach ($commonAbilities as $ability) {
            self::getAbilityConfig($ability);
        }
    }
    
    /**
     * Limpiar cache de configuraciones
     */
    public static function clearCache(): void
    {
        self::$config = null;
        self::$abilityConfigs = [];
    }
    
    public static function getAbilityConfig(string $ability): array
    {
        if (!isset(self::$abilityConfigs[$ability])) {
            self::$abilityConfigs[$ability] = self::getConfig()->get($ability, []);
        }
        return self::$abilityConfigs[$ability];
    }
    
    public static function getAbilityName(string $ability): string
    {
        $config = self::getAbilityConfig($ability);
        return $config['name'] ?? "§fUnknown Ability";
    }
    
    public static function getAbilityLore(string $ability): array
    {
        $config = self::getAbilityConfig($ability);
        return $config['lore'] ?? [];
    }
    
    public static function getAbilityCooldown(string $ability): int
    {
        $config = self::getAbilityConfig($ability);
        return $config['cooldown'] ?? 30;
    }
    
    /**
     * Obtener configuración booleana de una habilidad
     */
    public static function getAbilityBool(string $ability, string $key, bool $default = false): bool
    {
        $config = self::getAbilityConfig($ability);
        return $config[$key] ?? $default;
    }
    
    /**
     * Obtener configuración numérica de una habilidad
     */
    public static function getAbilityInt(string $ability, string $key, int $default = 0): int
    {
        $config = self::getAbilityConfig($ability);
        return $config[$key] ?? $default;
    }
    
    /**
     * Obtener configuración de string de una habilidad
     */
    public static function getAbilityString(string $ability, string $key, string $default = ""): string
    {
        $config = self::getAbilityConfig($ability);
        return $config[$key] ?? $default;
    }
    
    /**
     * Verificar si una habilidad está habilitada
     */
    public static function isAbilityEnabled(string $ability): bool
    {
        return self::getAbilityBool($ability, 'enabled', true);
    }
    
    /**
     * Obtener todas las habilidades configuradas
     */
    public static function getAllAbilities(): array
    {
        $config = self::getConfig();
        $abilities = [];
        
        foreach ($config->getAll() as $key => $value) {
            if (is_array($value) && isset($value['name'])) {
                $abilities[$key] = $value;
            }
        }
        
        return $abilities;
    }
    
    /**
     * Obtener estadísticas de configuración
     */
    public static function getConfigStats(): array
    {
        $allAbilities = self::getAllAbilities();
        $enabledCount = 0;
        $totalCooldown = 0;
        
        foreach ($allAbilities as $ability => $config) {
            if (self::isAbilityEnabled($ability)) {
                $enabledCount++;
            }
            $totalCooldown += $config['cooldown'] ?? 30;
        }
        
        return [
            'total_abilities' => count($allAbilities),
            'enabled_abilities' => $enabledCount,
            'disabled_abilities' => count($allAbilities) - $enabledCount,
            'average_cooldown' => count($allAbilities) > 0 ? round($totalCooldown / count($allAbilities), 2) : 0,
            'cached_configs' => count(self::$abilityConfigs)
        ];
    }
}