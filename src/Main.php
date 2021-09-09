<?php
declare(strict_types=1); //You SHOULD add this line for strict types.

namespace JinodkDevTeam\ExamplePlugin;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener{ //Main class MUST extends PluginBase, implement Listener for Event Handle

	protected bool $allowbreak = true;

	protected function onEnable(): void{ //This function MUST return void. You can make it public or protected.
		$this->getServer()->getPluginManager()->registerEvents($this, $this); // Register Event Handle for Main class ($this)

		$this->getServer()->getLogger()->info("ExamplePlugin is Enabled"); //Logg something to console when this plugin is enabled.
	}

	protected function onDisable() : void{ //This function MUST return void. You can make it public or protected.
		$this->getServer()->getLogger()->info("ExamplePlugin is Disabled"); //Logg something to console when this plugin is disabled.
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		switch($command->getName()){
			case "allowbreak":
				if ($this->allowbreak){
					$this->allowbreak = false;
					$sender->sendMessage("Disallow break blocks !");
				}else{
					$this->allowbreak = true;
					$sender->sendMessage("Allow break blocks !");
				}
				break;
			case "giveapple":
				if ($sender instanceof Player){ //Check if command sender is Player
					$this->giveApple($sender);
				}else{
					$sender->sendMessage("You must use this command as a player !");
				}
				break;
			case "pos":
				if ($sender instanceof Player){ //Check if command sender is Player
					$this->getPos($sender);
				}else{
					$sender->sendMessage("You must use this command as a player !");
				}
		}
		return true;
	}

	protected function giveApple(Player $player): void{ //Give apple to a player.
		$inv = $player->getInventory();
		$item = VanillaItems::APPLE(); //Get Apple Item
		if ($inv->canAddItem($item)){ //Check if player inventory can add this item
			$inv->addItem($item); //Give item to inventory
			$player->sendMessage("Done");
		}else{
			$player->sendMessage("Your inventory dont have enough space to get items.");
		}
	}

	protected function getPos(Player $player): void{ //Return player position info
		$pos = $player->getPosition();
		$player->sendMessage("X: " . $pos->getX());
		$player->sendMessage("Y: " . $pos->getY());
		$player->sendMessage("Z: " . $pos->getZ());
		$player->sendMessage("World: " . $pos->getWorld()->getDisplayName());
	}

	/******FOR MORE EXAMPLE*******/

	protected function feed(Player $player): void{ //Feed player
		$player->getHungerManager()->setFood(20);
	}

	protected function heal(Player $player): void{ //Heal player
		$player->setHealth(20);
	}

	/****************************/

	/**
	 * @param BlockBreakEvent $event
	 * @priority NORMAL
	 * @handleCancelled FALSE
	 */
	public function onBreak(BlockBreakEvent $event){
		/*
		 * @priority implement event priority:
		 * LOWEST->LOW->NORMAL->HIGH->HIGHEST->MONITOR
		 * @handleCancelled when it false, if this event was cancelled, this function will be ignored.
		 */
		if (!$this->allowbreak){ //If allow break is false, cancel this event
			$event->cancel();
		}
	}
}