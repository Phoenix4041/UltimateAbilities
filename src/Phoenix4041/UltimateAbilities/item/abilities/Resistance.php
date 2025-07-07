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
use pocketmine\world\particle\BlockBreakParticle;
use pocketmine\world\sound\AnvilUseSound;
use pocketmine\block\VanillaBlocks;

class Resistance extends AbilityItem
{
    public function __construct()
    {
        $config = Provider::getAbilityConfig('resistance');
        parent::__construct(
            $config['name'] ?? "§8§lResistance",
            VanillaItems::IRON_INGOT(),
            $config['cooldown'] ?? 45,
            $config['lore'] ?? [
                "§7Reduce el daño recibido",
                "§7durante 30 segundos",
                "",
                "§aClick derecho para usar"
            ]
        );
    }
    
    public function getAbilityName(): string
    {
        return "resistance";
    }
    
    protected function execute(Player $player, Vector3 $directionVector): void
    {
        // Aplicar efectos
        $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 30 * 20, 1));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::FIRE_RESISTANCE(), 30 * 20, 0));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::ABSORPTION(), 30 * 20, 0));
        
        // Efectos visuales
        $world = $player->getWorld();
        $pos = $player->getPosition();
        
        for ($i = 0; $i < 20; $i++) {
            $x = $pos->x + (mt_rand(-10, 10) / 10);
            $y = $pos->y + (mt_rand(0, 15) / 10);
            $z = $pos->z + (mt_rand(-10, 10) / 10);
            $world->addParticle(new Vector3($x, $y, $z), new BlockBreakParticle(VanillaBlocks::IRON_BLOCK()));
        }
        
        $world->addSound($pos, new AnvilUseSound());
        
        $this->sendMessage($player, "§8¡Resistance activado! Tu resistencia ha aumentado por 30 segundos!");
        
        // Notificar a jugadores cercanos
        foreach ($world->getNearbyEntities($player->getBoundingBox()->expandedCopy(10, 10, 10)) as $entity) {
            if ($entity instanceof Player && $entity !== $player) {
                $entity->sendMessage("§8¡{$player->getName()} ha activado Resistance!");
            }
        }
    }
}