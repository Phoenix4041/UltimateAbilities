<?php
// RedBull.php
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
use pocketmine\world\sound\FizzSound;

class RedBull extends AbilityItem
{
    public function __construct()
    {
        $config = Provider::getAbilityConfig('redbull');
        parent::__construct(
            $config['name'] ?? "§c§lRed Bull",
            VanillaItems::SPLASH_POTION(),
            $config['cooldown'] ?? 40,
            $config['lore'] ?? [
                "§7Te da alas y velocidad",
                "§7durante 20 segundos",
                "",
                "§aClick derecho para usar"
            ]
        );
    }
    
    public function getAbilityName(): string
    {
        return "redbull";
    }
    
    protected function execute(Player $player, Vector3 $directionVector): void
    {
        $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 20, 2));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::JUMP_BOOST(), 20 * 20, 2));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::LEVITATION(), 3 * 20, 0));
        
        $world = $player->getWorld();
        $pos = $player->getPosition();
        
        for ($i = 0; $i < 15; $i++) {
            $x = $pos->x + (mt_rand(-10, 10) / 10);
            $y = $pos->y + (mt_rand(0, 20) / 10);
            $z = $pos->z + (mt_rand(-10, 10) / 10);
            $world->addParticle(new Vector3($x, $y, $z), new FlameParticle());
        }
        
        $world->addSound($pos, new FizzSound());
        $this->sendMessage($player, "§c¡Red Bull te da alas! Velocidad y salto mejorados!");
    }
}