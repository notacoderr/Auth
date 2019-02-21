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
    
	public function onLine(PlayerJoinEvent $event) : void
	{
		$player = $event->getPlayer();
		$name = $player->getName();
		switch($player->getPlayerStatus())
		{
			case 1: //Unregistered
				//$this->main->forms->sendRegistration($player);
			break;
			
			case 2: //Logged out
				$player->sendMessage(
				TF::BOLD . TF::GOLD . "[ ATTENTION ]" . TF::EOL .
				TF::BOLD . TF::WHITE . "Please /login to use the ff:" . TF::EOL .
				TF::BOLD . TF::WHITE . "- Chat" . TF::EOL .
				TF::BOLD . TF::WHITE . "- Move" . TF::EOL .
				TF::BOLD . TF::WHITE . "- Run Commands" . TF::EOL .
				TF::BOLD . TF::WHITE . "- See" . TF::EOL
				);
			
				if(!in_array($name, $this->main->notLoggedIn))
				{
					$this->main->notLoggedIn[$name] = $this->main->microtime_int();
				}
			break;
			
			case 3: //Logged in
				switch($this->main->config->getNested("onLogin.message-type"))
				{
					case "msg":
						$player->sendMessage($this->main->config->getNested("onLogin.message-1"));
					break;
					
					case "popup":
						$player->sendPopup($this->main->config->getNested("onLogin.message-1"));
						$player->sendTip($this->main->config->getNested("onLogin.message-2"));
					break;
					
					case "title":
						$t = $this->main->config->getNested("onLogin.message-1");
						$st = $this->main->config->getNested("onLogin.message-2");
						$player->addTitle($t , $st);
					break;
				}
			break;
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
		if($event->getPlayer()->getPlayerStatus() <> 3) $event->setCancelled();
	}
	
	public function ifChatting(PlayerChatEvent $event) : void
	{
		if($event->getPlayer()->getPlayerStatus() <> 3)
		{
			$event->setCancelled();
			$event->getPlayer()->sendMessage(TF::BOLD . TF::GOLD . "You have to Login to use the Chat! [/login]");
		}
	}
	
	public function ifTryingCommands(PlayerCommandPreprocessEvent $event) : void
    {
		$command = explode(" ", $event->getMessage());
		if($event->getPlayer()->getPlayerStatus() <> 3 && $command[0] != "/login")
		{
			$event->setCancelled();
			$event->getPlayer()->sendMessage(TF::BOLD . TF::GOLD . "You have to Login to use the Chat! [/login]");
		}
    }
}

?>
