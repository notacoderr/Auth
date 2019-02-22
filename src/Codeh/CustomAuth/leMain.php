<?php

namespace Codeh\CustomAuth;

use pocketmine\Server;
use pocketmine\Player;

use pocketmine\utils\{config, TextFormat as T};
use pocketmine\plugin\PluginBase;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class leMain extends PluginBase
{

	public $data, $config, $grace, $customAuthName, $notLoggedIn = [], $maxChar, $minChar, $remember = true;
	
	private $ip, $port;
	
    public function onLoad()
    {
		$this->getLogger()->info("§eLoading......");
    }
	
	public function onEnable()
	{
		$this->saveDefaultConfig();
		$this->config = new Config($this->getDataFolder()."config.yml", Config::YAML);
		$this->config->getAll();
		$this->hasValidConfig();
	}
	
	public function onDisable()
	{
		$this->getLogger()->info("§6CUSTOM AUTH + USERNAME§c has been disabled!");   
	}

	private function hasValidConfig() : bool
	{
		if($this->config->get("config-version") <> 3)
		{
			$this->getLogger()->critical("Oops, it seems that your config is not valid...");
			$this->getServer()->getPluginManager()->disablePlugin($this);
			return false;

		} else {
			$this->forms = new leForms($this);
			
			$this->data = new leDataHandler($this);
			$this->data->init();
			
			$this->getServer()->getPluginManager()->registerEvents(new leListener($this), $this);
			
			
			$this->grace = $this->config->getNested("unAuth.grace-period");
			if($this->grace > 0) $this->getScheduler()->scheduleRepeatingTask(new leTask($this), 1200);
			
			$this->minChar = $this->config->getNested("registration.restriction.min_char");
			$this->maxChar = $this->config->getNested("registration.restriction.max_char");
			$this->remember = $this->config->get("remember_Login");
			$this->customAuthName = $this->config->get("custom_name");
			
			$this->ip = $this->config->get("server-ip");
			$this->port = $this->config->get("server-port");
			$this->getLogger()->Info("§6CUSTOM AUTH + USERNAME§a has been enabled!");
			return true;
		}
		return false;
	}
	
	public function microtime_int()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((int)$usec + (int)$sec);
    }
	
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool
	{
        $name = $command->getName();
		if($sender instanceof Player || $sender instanceof leCustomPlayer)
		{
			switch($name)
			{
				case "login": 
					if($this->data->isCustomUsername($sender->getName()) == false)
					{
						$sender->sendMessage($this->customAuthName. $this->config->getNested("otherMessages.notRegistered"));
					} else {
						if($sender->isLoggedIn)
						{
							$sender->sendMessage($this->customAuthName. $this->config->getNested("otherMessages.alreadyLoggedIn"));
						} else {
							$this->forms->loginForm($sender);
						}
					}
				break;
				
				case "logout":
					$sender->setPlayerTries(5); //reset tries
					$this->data->setIpAddress($sender->getName(), $sender->getAddress()); //updates ipAddress
					$sender->isLoggedIn = false; //Mark as Logged out
					
					if($this->grace > 0)
					{
						if(!in_array($sender->getName(), $this->notLoggedIn))
						{
							$this->notLoggedIn[$sender->getName()] = $this->microtime_int();
						}
					}
					
					$sender->sendMessage($this->customAuthName. $this->config->getNested("otherMessages.playerLogout"));
				break;
				
				case "register":
					if($this->data->isCustomUsername($sender->getName()))
					{
						if($sender->isLoggedIn)
						{
							$sender->sendMessage($this->customAuthName. $this->config->getNested("otherMessages.alreadyLoggedIn"));
						} else {
							$sender->sendMessage($this->customAuthName. $this->config->getNested("otherMessages.alreadyRegistered"));
						}
					} else {
						$this->forms->registrationForm($sender);
					}
				break;
			}
		}
        return true;
    }
	
	public function processLogin($customPlayer, string $password)
	{
		$customPlayerName = $customPlayer->getName();
		if($this->data->getPassword($customPlayerName) != hash("md5", $password))
		{
			if(($tries = $customPlayer->getPlayerTries()) >= 2)
			{
				$customPlayer->sendMessage($this->customAuthName . $this->config->getNested("otherMessages.incorrectPW"));
				$customPlayer->setPlayerTries(--$tries);
			} else {
				$customPlayer->kick("Exceeded login attempt");
			}
		} else {
			$this->LoginSuccess($customPlayer);
		}
	}
	
	public function processRegistration($player, array $info)
	{
		if($this->data->getUsername($info[1]) == false)
		{
			$this->data->setUsername($player->getName(), $info[1]);
			$this->data->setPassword($info[1], $info[2]);
			$this->data->setDiscord($info[1], ($info[3] == "") ? null : $info[3]);
			$this->data->setIpAddress($info[1], $player->getAddress());
			
			$player->transfer($this->ip, (int) ($this->port ?? 19132));
		} else {
			$player->sendMessage($this->customAuthName . $this->config->getNested("otherMessages.usernameNotAvailable"));
		}
	}
	
	public function LoginSuccess($customPlayer) : void
	{
		$customPlayerName = $customPlayer->getName();
		$customPlayer->setPlayerTries(5); //reset tries
		$this->data->setIpAddress($customPlayerName, $customPlayer->getAddress()); //updates ipAddress
		$customPlayer->isLoggedIn = true; //Mark as Logged in
		
		if(in_array($customPlayerName, $this->notLoggedIn)) //remove from the array
		{
			unset($this->main->notLoggedIn[$customPlayerName]);
		}
		
		switch($this->config->getNested("onLogin.message.type"))
		{
			case "msg":
				$customPlayer->sendMessage($this->config->getNested("onLogin.message.text"));
			break;
			
			case "title":
				$title = $this->config->getNested("onLogin.message.header");
				$subtitle = $this->config->getNested("onLogin.message.text");
				$customPlayer->addTitle($title, $subtitle);
			break;
			
			case "popup":
				$customPlayer->sendTip($this->config->getNested("onLogin.message.header"));
				$customPlayer->sendPopup($this->config->getNested("onLogin.message.text"));
			break;
		}
	}
}
?>
