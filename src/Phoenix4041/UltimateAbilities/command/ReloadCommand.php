<?php

declare(strict_types=1);

namespace Phoenix4041\UltimateAbilities\command;

use Phoenix4041\UltimateAbilities\provider\Provider;
use Phoenix4041\UltimateAbilities\UltimateAbilities;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class ReloadCommand extends Command
{
    private UltimateAbilities $plugin;
    
    public function __construct(UltimateAbilities $plugin)
    {
        parent::__construct(
            "uareload",
            "Recarga la configuración del plugin UltimateAbilities",
            "/uareload",
            ["uar"]
        );
        $this->plugin = $plugin;
        $this->setPermission("ultimateabilities.reload");
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        if (!$this->testPermission($sender)) {
            return false;
        }
        
        try {
            $startTime = microtime(true);
            
            // Recargar configuraciones
            $this->plugin->reloadConfig();
            
            // Recargar configuraciones del Provider si existe
            if (method_exists(Provider::class, 'reload')) {
                Provider::reload();
            }
            
            // Limpiar cooldowns activos (opcional)
            if (method_exists($this->plugin, 'clearAllCooldowns')) {
                $this->plugin->clearAllCooldowns();
            }
            
            $endTime = microtime(true);
            $executionTime = round(($endTime - $startTime) * 1000, 2);
            
            $sender->sendMessage(TextFormat::GREEN . "✓ Plugin UltimateAbilities recargado exitosamente!");
            $sender->sendMessage(TextFormat::GRAY . "Tiempo de ejecución: " . TextFormat::YELLOW . $executionTime . "ms");
            
            // Log para consola
            $this->plugin->getLogger()->info("Plugin recargado por " . $sender->getName() . " en {$executionTime}ms");
            
            return true;
            
        } catch (\Exception $e) {
            $sender->sendMessage(TextFormat::RED . "✗ Error al recargar el plugin: " . $e->getMessage());
            $this->plugin->getLogger()->error("Error en reload: " . $e->getMessage());
            return false;
        }
    }
}