<?php

declare(strict_types=1);

namespace Phoenix4041\UltimateAbilities\item;

use Phoenix4041\UltimateAbilities\UltimateAbilities;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\block\BlockBreakInfo;

abstract class AbilityItem
{
    protected string $name;
    protected Item $item;
    protected int $cooldown;
    protected array $lore;
    
    public function __construct(string $name, Item $item, int $cooldown, array $lore)
    {
        $this->name = $name;
        $this->item = $item;
        $this->cooldown = $cooldown;
        $this->lore = $lore;
        
        $this->setupItem();
    }
    
    private function setupItem(): void
    {
        $this->item->setCustomName($this->name);
        $this->item->setLore($this->lore);
        
        # Add glow effect
        $this->item->addEnchantment(new \pocketmine\item\enchantment\EnchantmentInstance(
            \pocketmine\data\bedrock\EnchantmentIdMap::getInstance()->fromId(-1), 1
        ));
        
        # Add NBT tag to identify as ability item
        $nbt = $this->item->getNamedTag();
        $nbt->setString('ultimate_ability', $this->getAbilityName());
        $this->item->setNamedTag($nbt);
    }
    
    public function getItem(): Item
    {
        return clone $this->item;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getCooldown(): int
    {
        return $this->cooldown;
    }
    
    public function getLore(): array
    {
        return $this->lore;
    }
    
    abstract public function getAbilityName(): string;
    
    public function onUse(Player $player, Vector3 $directionVector): void
    {
        # Check cooldown
        if ($this->isOnCooldown($player)) {
            $remaining = $this->getRemainingCooldown($player);
            $player->sendMessage("§cDebes esperar §e{$remaining}s §cpara usar esta habilidad again.");
            return;
        }
        
        # Execute ability
        $this->execute($player, $directionVector);
        
        # Set cooldown
        $this->setCooldown($player);
    }
    
    public function onInteract(Player $player, int $action, Block $block): void
    {
        # Override in subclasses if needed
    }
    
    public function onAttack(Player $damager, Entity $victim, EntityDamageByEntityEvent $event): void
    {
        # Override in subclasses if needed
    }
    
    abstract protected function execute(Player $player, Vector3 $directionVector): void;
    
    protected function isOnCooldown(Player $player): bool
    {
        $session = UltimateAbilities::getInstance()->getSessionManager()->getSession($player);
        if ($session === null) {
            return false;
        }
        
        return $session->hasCooldown($this->getAbilityName());
    }
    
    protected function getRemainingCooldown(Player $player): int
    {
        $session = UltimateAbilities::getInstance()->getSessionManager()->getSession($player);
        if ($session === null) {
            return 0;
        }
        
        return $session->getRemainingCooldown($this->getAbilityName());
    }
    
    protected function setCooldown(Player $player): void
    {
        $session = UltimateAbilities::getInstance()->getSessionManager()->getSession($player);
        if ($session === null) {
            return;
        }
        
        $session->setCooldown($this->getAbilityName(), $this->cooldown);
    }
    
    protected function sendMessage(Player $player, string $message): void
    {
        $player->sendMessage("§8[§bUltimateAbilities§8] §r" . $message);
    }
}