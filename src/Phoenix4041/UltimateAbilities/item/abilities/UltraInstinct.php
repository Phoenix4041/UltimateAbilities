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
use pocketmine\world\particle\FlameParticle;
use pocketmine\world\sound\BlazeShootSound;

class UltraInstinct extends AbilityItem
{
    public function __construct()
    {
        $config = Provider::getAbilityConfig('ultrainstinct');
        parent::__construct(
            $config['name'] ?? "§d§lUltra Instinct",
            VanillaItems::LIGHT_GRAY_DYE(),
            $config['cooldown'] ?? 60,
            $config['lore'] ?? [
                "§7Aumenta tu velocidad y",
                "§7reflejos durante 15 segundos",
                "",
                "§aClick derecho para usar"
            ]
        );
    }
    
    public function getAbilityName(): string
    {
        return "ultrainstinct";
    }
    
    protected function execute(Player $player, Vector3 $directionVector): void
    {
        // Aplicar efectos
        $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 15 * 20, 2));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::JUMP_BOOST(), 15 * 20, 1));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 15 * 20, 0));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 15 * 20, 0));
        
        // Efectos visuales
        $world = $player->getWorld();
        $pos = $player->getPosition();
        
        for ($i = 0; $i < 30; $i++) {
            $x = $pos->x + (mt_rand(-20, 20) / 10);
            $y = $pos->y + (mt_rand(0, 30) / 10);
            $z = $pos->z + (mt_rand(-20, 20) / 10);
            $world->addParticle(new Vector3($x, $y, $z), new FlameParticle());
        }
        
        $world->addSound($pos, new BlazeShootSound());
        
        $this->sendMessage($player, "§d¡Ultra Instinct activado! Velocidad y reflejos mejorados por 15 segundos!");
        
        // Notificar a jugadores cercanos
        foreach ($world->getNearbyEntities($player->getBoundingBox()->expandedCopy(15, 15, 15)) as $entity) {
            if ($entity instanceof Player && $entity !== $player) {
                $entity->sendMessage("§d¡{$player->getName()} ha activado Ultra Instinct!");
            }
        }
    }
}