<?php

declare(strict_types=1);

namespace Phoenix4041\UltimateAbilities\provider;

use Phoenix4041\UltimateAbilities\UltimateAbilities;
use pocketmine\utils\Config;

class Provider
{
    private static Config $config;
    
    public static function init(): void
    {
        self::$config = new Config(UltimateAbilities::getInstance()->getDataFolder() . "config.yml", Config::YAML);
    }
    
    public static function getConfig(): Config
    {
        return self::$config;
    }
    
    public static function save(): void
    {
        self::$config->save();
    }
    
    public static function getAbilityConfig(string $ability): array
    {
        return self::$config->get($ability, []);
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