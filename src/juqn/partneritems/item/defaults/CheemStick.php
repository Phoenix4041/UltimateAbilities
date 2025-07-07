<?php

declare(strict_types=1);

namespace juqn\partneritems\item\defaults;

use juqn\partneritems\PartnerItems;
use juqn\partneritems\item\Item;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class CheemStick extends Item
{
    
    /** @var array */
    private array $hits = [];
    
    /**
     * CheemStick construct.
     */
    public function __construct()
    {
        parent::__construct('&4Cheems Stick', VanillaItems::STICK());
    }
    
    /**
     * @return int
     */
    public function getTime(): int
    {
        return 90;
    }
    
    /**
     * @param Player $player
     * @param Vector3 $directionVector
     * @return ItemUseResult
     */
    public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult
    {
        return ItemUseResult::NONE();
    }
    
    /**
     * @param EntityDamageEvent $event
     */
    public function damage(EntityDamageEvent $event): void
    {
        $player = $event->getEntity();
        
        if (!$player instanceof Player) {
            return;
        }
        $item = $player->getInventory()->getItemInHand();
        
        if ($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            
            if (!$damager instanceof Player) {
                return;
            }
            $session = PartnerItems::getInstance()->getSessionManager()->getSession($player);
        
            if ($session !== null) {
                $cooldown = $session->getCooldown($this->getName());
            
                if ($cooldown !== null && !$cooldown->isExpired()) {
                    $player->sendMessage(TextFormat::colorize('&cTienes tiempo de reutilización para usar este item'));
                    return;
                }
                
                $global = $session->getCooldown("Global");

                if ($global !== null && !$global->isExpired()) {
                    $player->sendMessage(TextFormat::colorize('&cTienes cooldawn global'));
                    return;
                }
            }
            
            if (!isset($this->hits[$player->getName()])) {
                $this->hits[$player->getName()] = [
                    'count' => 1,
                    'time' => time() + 1
                ];
                return;
            }
            $data = $this->hits[$player->getName()];
            
            if ($data['time'] < time()) {
                $this->hits[$player->getName()] = [
                    'count' => 1,
                    'time' => time() + 1
                ];
                return;
            }
            $data['count'] += 1;
            $data['time'] = time() + 1;
            
            if ($data['count'] >= 1) {
                $effects = $damager->getEffects();
                $effects->add(new EffectInstance(VanillaEffects::SLOWNESS(), 20 * 10, 1));
                $effects->add(new EffectInstance(VanillaEffects::WEAKNESS(), 20 * 10, 1));
                unset($this->hits[$player->getName()]);
                
                $item->pop();
                $player->getInventory()->setItemInHand($item);
                
                $session->addCooldown($this->getName(), $this->getTime());
                $session->addCooldown("Global", 10);
                $player->sendMessage(TextFormat::colorize('&eHas usado la ability ' . $this->getName() . PHP_EOL . '&eAhora tienes un cooldawn de: ' . gmdate('i:s' . $this->getTime())));
                return;
            }
            $this->hits[$player->getName()] = $data;
        }
    }
}