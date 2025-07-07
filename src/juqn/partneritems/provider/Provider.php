<?php

declare(strict_types=1);

namespace juqn\partneritems\provider;

use juqn\partneritems\PartnerItems;
use pocketmine\utils\Config;

class Provider
{
    
    static public function init(): void
    {
        $plugin = PartnerItems::getInstance();
        
        if (!is_dir($plugin->getDataFolder() . 'players')) {
            @mkdir($plugin->getDataFolder() . 'players');
        }
    }
    
    static public function save(): void
    {
        $plugin = PartnerItems::getInstance();
        
        foreach ($plugin->getSessionManager()->getSessions() as $xuid => $session) {
            $config = new Config($plugin->getDataFolder() . 'players/' . $xuid . '.json', Config::JSON);
            $config->setAll($session->serializeData());
            $config->save();
        }
    }
    
    /**
     * @return array
     */
    static public function getPlayers(): array
    {
        $players = [];
        $plugin = PartnerItems::getInstance();
        $files = glob($plugin->getDataFolder() . 'players/*.json');
        
        foreach ($files as $file) {
            $players[basename($file, '.json')] = (new Config($plugin->getDataFolder() . 'players/' . basename($file), Config::JSON))->getAll();
        }
        return $players;
    }
}