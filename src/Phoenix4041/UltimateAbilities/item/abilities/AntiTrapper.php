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
use pocketmine\world\sound\FizzSound;

class AntiTrapper extends AbilityItem
{
    public function __construct()
    {
        $config = Provider::getAbilityConfig('antitrapper');
        parent::__construct(
            $config['name'] ?? "§6§lAnti Trapper",
            VanillaItems::STICK(),
            $config['cooldown'] ?? 35,
            $config['lore'] ?? [
                "§7Impide romper y poner bloques",
                "§7a jugadores en un radio de 10 bloques",
                "§7durante 10 segundos",
                "",
                "§aClick derecho para usar"
            ]
        );
    }
    
    public function getAbilityName(): string
    {
        return "antitrapper";
    }
    
    protected function execute(Player $player, Vector3 $directionVector): void
    {
        $session = \Phoenix4041\UltimateAbilities\UltimateAbilities::getInstance()->getSessionManager()->getSession($player);
        if ($session !== null) {
            $session->setEffect('antitrapper', 10);
        }
        
        // Efectos visuales y sonoros
        $world = $player->getWorld();
        $pos = $player->getPosition();
        
        // Partículas en círculo para mostrar el área de efecto
        for ($i = 0; $i < 360; $i += 30) {
            $x = $pos->x + (10 * cos(deg2rad($i)));
            $z = $pos->z + (10 * sin(deg2rad($i)));
            $world->addParticle(new Vector3($x, $pos->y + 1, $z), new FlameParticle());
        }
        
        $world->addSound($pos, new FizzSound());
        
        $this->sendMessage($player, "§6¡Anti Trapper activado! Bloqueando construcción en área por 10 segundos!");
        
        // Mensaje a jugadores cercanos
        foreach ($world->getPlayers() as $nearPlayer) {
            if ($nearPlayer !== $player && $nearPlayer->getPosition()->distance($pos) <= 10) {
                $nearPlayer->sendMessage("§c¡No puedes romper ni poner bloques! Hay un Anti Trapper activo cerca.");
            }
        }
    }
}