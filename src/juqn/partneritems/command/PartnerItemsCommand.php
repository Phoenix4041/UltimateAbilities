<?php

declare(strict_types=1);

namespace juqn\partneritems\command;

use juqn\partneritems\PartnerItems;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PartnerItemsCommand extends Command
{
    
    /**
     * PartnerItemsCommand construct.
     */
    public function __construct()
    {
        parent::__construct('abilities', 'Command for partneritems');
        $this->setPermission("give.partneritems");
    }
    
    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) {
            return;
        }
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $items = array_values(PartnerItems::getInstance()->getItemManager()->getItems());
        $menu->getInventory()->setContents(array_map(function ($item) {
            return $item->getItem();
        }, $items));
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();
            
            if ($player->hasPermission('give.partneritems')) {
                if ($player->getInventory()->canAddItem($item)) {
                    $player->getInventory()->addItem($item);
                }
            }
            return $transaction->discard();
        });
        $menu->send($sender, TextFormat::colorize('&gAbilities'));
    }
}