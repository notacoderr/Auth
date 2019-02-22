<?php

namespace Codeh\CustomAuth;

use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerCreationEvent;

use pocketmine\utils\TextFormat as TF;

class leListener implements Listener{
    
    private $main;

    public function __construct(\Codeh\CustomAuth\leMain $core)
    {
		$this->main = $core;
    }
	
	/**
	 * @param PlayerCreationEvent $event
	 * @priority HIGHEST
	 */
	public function onPlayerCreation(PlayerCreationEvent $event){
		$event->setPlayerClass(leCustomPlayer::class);
	}
    
	public function onLine(PlayerJoinEvent $event)
	{
		$player = $event->getPlayer();
		$name = $player->getName();
		if($this->main->data->isCustomUsername($name) == false)//checks if player is not yet registered
		{
			$string = $this->main->config->getNested("otherMessages.useRegister");
			$string = str_replace("{NL}", TF::EOL, $string);
			$player->sendMessage($string);
		} else {

			if($this->main->remember && $this->main->data->getIpAddress($name) == $player->getAddress())
			{
				return $this->main->LoginSuccess($player);
			}

			$string = $this->main->config->getNested("otherMessages.useLogin");
			$string = str_replace("{NL}", TF::EOL, $string);
			$player->sendMessage($string);
			
			if($this->main->grace > 0)
			{
				if(!in_array($name, $this->main->notLoggedIn))
				{
					$this->main->notLoggedIn[$name] = $this->main->microtime_int();
				}
			}
		}

	}
	
	public function offLine(PlayerQuitEvent $event) : void
	{
		$name = $event->getPlayer()->getName();
		if(in_array($name, $this->main->notLoggedIn))
		{
			unset($this->main->notLoggedIn[$name]);
		}
	}
	
	public function ifMoving(PlayerMoveEvent $event) : void
	{
		if($event->getPlayer()->isLoggedIn == false) $event->setCancelled();
	}
	
	public function ifChatting(PlayerChatEvent $event) : void
	{
		if($event->getPlayer()->isLoggedIn == false)
		{
			$event->setCancelled();
			$event->getPlayer()->sendMessage($this->main->config->getNested("otherMessages.notLoggedIn"));
		}
	}
	
	public function ifTryingCommands(PlayerCommandPreprocessEvent $event)
    {
		if($event->getPlayer()->isLoggedIn == false)
		{
			$command = explode(" ", $event->getMessage());
			if($command[0] == "/login" || $command[0] == "/register") return;
			$event->setCancelled();
			$event->getPlayer()->sendMessage($this->main->config->getNested("otherMessages.notLoggedIn"));
		}
    }
}

?>
