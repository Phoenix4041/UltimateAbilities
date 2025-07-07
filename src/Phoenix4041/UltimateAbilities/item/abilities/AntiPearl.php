<?php

declare(strict_types=1);

namespace Phoenix4041\UltimateAbilities\item\abilities;

use Phoenix4041\UltimateAbilities\item\AbilityItem;
use Phoenix4041\UltimateAbilities\provider\Provider;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\particle\FlameParticle;
use pocketmine\world\sound\AnvilBreakSound;

class AntiPearl extends AbilityItem
{
    public function __construct()
    {
        $config = Provider::getAbilityConfig('antipearl');
        parent::__construct(
            $config['name'] ?? "§c§lAnti Pearl",
            VanillaItems::SHEARS(),
            $config['cooldown'] ?? 30,
            $config['lore'] ?? [
                "§7Bloquea el uso de ender pearls",
                "§7en un área durante 10 segundos",
                "",
                "§aClick derecho para usar"
            ]
        );
    }
    
    public function getAbilityName(): string
    {
        return "antipearl";
    }
    
    protected function execute(Player $player, Vector3 $directionVector): void
    {
        $world = $player->getWorld();
        $pos = $player->getPosition();
        
        // Crear zona anti-pearl
        $session = \Phoenix4041\UltimateAbilities\UltimateAbilities::getInstance()->getSessionManager()->getSession($player);
        if ($session !== null) {
            $session->setEffect('antipearl_zone', 10);
        }
        
        // Efectos visuales
        for ($i = 0; $i < 20; $i++) {
            $x = $pos->x + mt_rand(-5, 5);
            $y = $pos->y + mt_rand(0, 3);
            $z = $pos->z + mt_rand(-5, 5);
            $world->addParticle(new Vector3($x, $y, $z), new FlameParticle());
        }
        
        $world->addSound($pos, new AnvilBreakSound());
        
        $this->sendMessage($player, "§c¡Zona Anti-Pearl activada durante 10 segundos!");
        
        // Notificar a jugadores cercanos
        foreach ($world->getNearbyEntities($player->getBoundingBox()->expandedCopy(10, 10, 10)) as $entity) {
            if ($entity instanceof Player && $entity !== $player) {
                $entity->sendMessage("§c¡{$player->getName()} ha activado una zona Anti-Pearl!");
            }
        }
    }
}