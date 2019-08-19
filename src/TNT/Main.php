<?php

declare(strict_types=1);

namespace TNT;

use pocketmine\block\Solid;
use pocketmine\entity\Entity;
use pocketmine\entity\object\FallingBlock;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\entity\object\PrimedTNT;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener{

    public function onEnable(): void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

	public function onInteract(PlayerInteractEvent $event): void{
	    $player = $event->getPlayer();
	    if($player->getInventory()->getItemInHand()->getId() === Item::TNT){
	        /* @var PrimedTNT $entity */
	        $entity = Entity::createEntity("PrimedTNT", $player->getLevel(), Entity::createBaseNBT($player));
	        $entity->setMotion($player->getDirectionVector()->normalize()->multiply(2));
	        $entity->spawnToAll();
        }
    }

    public function onExplode(EntityExplodeEvent $event): void{
        $event->setCancelled();
        foreach($event->getBlockList() as $block){
            if($block instanceof Solid){
                $nbt = Entity::createBaseNBT($block);
                $nbt->setInt("TileID", $block->getId());
                $nbt->setByte("Data", $block->getDamage());
                $entity = new FallingBlock($event->getEntity()->getLevel(), $nbt);
                $entity->setMotion(new Vector3(0, 1, 0));
                $entity->spawnToAll();
            }
        }
    }
}