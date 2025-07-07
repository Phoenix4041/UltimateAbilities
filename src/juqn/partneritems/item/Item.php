<?php

declare(strict_types=1);

namespace juqn\partneritems\item;

use juqn\partneritems\PartnerItems;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item as PMItem;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemUseResult;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\StringToItemParser;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class Item
{

    /**
     * @param string $name
     * @param PMItem $items
     */
    public function __construct(private string $name, private PMItem $items)
    {
        $this->name = TextFormat::colorize($name);
    }
    
    /**
     * @param Player $player
     */
    public function pop(Player $player): void
    {
        $item = $player->getInventory()->getItemInHand();
        $item->pop();
        $player->getInventory()->setItemInHand($item);
    }
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return TextFormat::clean($this->name);
    }
    
    /**
     * @return PMItem
     */
    public function getItem(): PMItem{
        $item = $this->items;
        $item->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(-1)));
        $item->setCustomName(TextFormat::colorize('&r' . $this->name));

        $itemlore = PartnerItems::getInstance()->getConfig()->get(str_replace([" "], [""], TextFormat::clean($this->name)));
        $l = [];
        if (is_array($itemlore) || is_object($itemlore)) {
            foreach ((array)$itemlore as $loreLine) {
                $l[] = TextFormat::colorize($loreLine);
            }
        }
        $item->setLore($l);

        $namedtag = $item->getNamedTag();
        $namedtag->setString('partner_item', TextFormat::clean($this->name));
        $item->setNamedTag($namedtag);
        
        return $item;
    }
    
    /**
     * @return int
     */
    public function getTime(): int
    {
        return 0;
    }
    
    /**
     * @param Player $player
     * @param Vector3 $directionVector
     * @return ItemUseResult
     */
    public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult
    {
        $session = PartnerItems::getInstance()->getSessionManager()->getSession($player);
        
        if ($session !== null) {
            $cooldown = $session->getCooldown($this->getName());
            
            if ($cooldown !== null && !$cooldown->isExpired()) {
                $player->sendMessage(TextFormat::colorize('&cTienes tiempo de reutilización para usar este artículo'));
                return ItemUseResult::FAIL();
            }

            $global = $session->getCooldown("Global");
            
            if ($global !== null && !$global->isExpired()) {
                $player->sendMessage(TextFormat::colorize('&cTienes cooldown global'));
                return ItemUseResult::FAIL();
            }
        }
        $session->addCooldown($this->getName(), $this->getTime());
        $session->addCooldown("Global", 10);
        $player->sendMessage(TextFormat::colorize('&eHas usado la habilidad ' . $this->getName() . PHP_EOL . '&eAhora tienes un tiempo de reutilización: ' . gmdate('i:s' . $this->getTime())));
        return ItemUseResult::SUCCESS();
    }
    
    /**
     * @param EntityDamageEvent $event
     */
    public function damage(EntityDamageEvent $event): void
    {
    }
}