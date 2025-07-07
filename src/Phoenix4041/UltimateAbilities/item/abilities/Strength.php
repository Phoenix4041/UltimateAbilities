<?php

declare(strict_types=1);

namespace Phoenix4041\UltimateAbilities\item\abilities;

use Phoenix4041\UltimateAbilities\item\AbilityItem;
use Phoenix4041\UltimateAbilities\provider\Provider;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\particle\RedstoneParticle;
use pocketmine\world\sound\BlazeShootSound;

class Strength extends AbilityItem
{
    public function __construct()
    {
        $config = Provider::getAbilityConfig('strength');
        parent::__construct(
            $config['name'] ?? "§4§lStrength",
            VanillaItems::BLAZE_POWDER(),
            $config['cooldown'] ?? 45,
            $config['lore'] ?? [
                "§7Aumenta tu fuerza",
                "§7durante 30 segundos",
                "",
                "§aClick derecho para usar"
            ]
        );
    }
    
    public function getAbilityName(): string
    {
        return "strength";
    }
    
    protected function execute(Player $player, Vector3 $directionVector): void
    {
        // Aplicar efectos
        $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 30 * 20, 1));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::HASTE(), 30 * 20, 0));
        
        // Efectos visuales
        $world = $player->getWorld();
        $pos = $player->getPosition();
        
        for ($i = 0; $i < 25; $i++) {
            $x = $pos->x + (mt_rand(-15, 15) / 10);
            $y = $pos->y + (mt_rand(0, 20) / 10);
            $z = $pos->z + (mt_rand(-15, 15) / 10);
            $world->addParticle(new Vector3($x, $y, $z), new RedstoneParticle());
        }
        
        $world->addSound($pos, new BlazeShootSound());
        
        $this->sendMessage($player, "§4¡Strength activado! Tu fuerza ha aumentado por 30 segundos!");
        
        // Notificar a jugadores cercanos
        foreach ($world->getNearbyEntities($player->getBoundingBox()->expandedCopy(10, 10, 10)) as $entity) {
            if ($entity instanceof Player && $entity !== $player) {
                $entity->sendMessage("§4¡{$player->getName()} ha activado Strength!");
            }
        }
    }
}