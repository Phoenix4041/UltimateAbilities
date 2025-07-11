<?php

declare(strict_types=1);

namespace Phoenix4041\UltimateAbilities\item\abilities;

use Phoenix4041\UltimateAbilities\item\AbilityItem;
use Phoenix4041\UltimateAbilities\provider\Provider;
use Phoenix4041\UltimateAbilities\UltimateAbilities;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class Alcohol extends AbilityItem
{
    public function __construct()
    {
        // DEBUG: Verificar si el archivo existe y su contenido
        $plugin = UltimateAbilities::getInstance();
        $configPath = $plugin->getDataFolder() . "config.yml";
        
        $plugin->getLogger()->info("DEBUG - Config file path: " . $configPath);
        $plugin->getLogger()->info("DEBUG - Config file exists: " . (file_exists($configPath) ? "YES" : "NO"));
        
        if (file_exists($configPath)) {
            $fileContent = file_get_contents($configPath);
            $plugin->getLogger()->info("DEBUG - Config file content (first 500 chars): " . substr($fileContent, 0, 500));
        }
        
        // Cargar configuración
        $config = Provider::getAbilityConfig('alcohol');
        
        // DEBUG: Log para ver qué valores se están cargando
        $plugin->getLogger()->info("DEBUG - Alcohol config loaded:");
        $plugin->getLogger()->info("Name: " . ($config['name'] ?? 'NOT FOUND'));
        $plugin->getLogger()->info("Cooldown: " . ($config['cooldown'] ?? 'NOT FOUND'));
        $plugin->getLogger()->info("Lore lines: " . (isset($config['lore']) ? count($config['lore']) : 'NOT FOUND'));
        
        // Valores por defecto más claros
        $name = $config['name'] ?? "§5§lAlcohol";
        $cooldown = $config['cooldown'] ?? 30;
        $lore = $config['lore'] ?? [
            "§7Te da visión nocturna pero",
            "§7reduce tu precisión",
            "",
            "§aClick derecho para usar"
        ];
        
        // Log del cooldown final que se usará
        $plugin->getLogger()->info("DEBUG - Final cooldown being used: " . $cooldown);
        
        parent::__construct(
            $name,
            VanillaItems::POTION(),
            $cooldown,
            $lore
        );
    }
    
    public function getAbilityName(): string
    {
        return "alcohol";
    }
    
    protected function execute(Player $player, Vector3 $directionVector): void
    {
        // También log cuando se ejecuta para verificar el cooldown
        $plugin = UltimateAbilities::getInstance();
        $plugin->getLogger()->info("DEBUG - Alcohol executed, cooldown should be: " . $this->getCooldown());
        
        $player->getEffects()->add(new EffectInstance(VanillaEffects::NIGHT_VISION(), 60 * 20, 0));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::NAUSEA(), 15 * 20, 0));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 20 * 20, 0));
        
        $this->sendMessage($player, "§5¡Alcohol activado! Visión nocturna pero con efectos secundarios!");
        $this->sendMessage($player, "§7Cooldown: " . $this->getCooldown() . " segundos");
    }
}