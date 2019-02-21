<?php

namespace Codeh\CustomAuth;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\utils\config;
use pocketmine\plugin\PluginBase;

class leMain extends PluginBase
{

	public $data, $config, $grace, $customAuthName, $notLoggedIn = [], $maxChar, $minChar, $maxAccount, $remember = true;
	
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
		if($this->config->get("config-version") <> 2)
		{
			$this->getLogger()->critical("Oops, it seems that your config is not valid...");
			$this->getServer()->getPluginManager()->disablePlugin($this);
			return false;
			
		} else {
			$this->forms = new leForms($this);
			$this->forms->init();
			
			$this->data = new leDataHandler($this);
			$this->data->init();
			
			$this->getServer()->getPluginManager()->registerEvents(new leListener($this), $this);
			$this->getScheduler()->scheduleRepeatingTask(new leTask($this), 1200);
			
			$this->grace = $this->config->getNested("unAuth.grace-period");
			$this->minChar = $this->config->getNested("registration.restriction.min_char");
			$this->maxChar = $this->config->getNested("registration.restriction.max_char");
			$this->maxAccount = $this->config->getNested("registration.restriction.max_account");
			$this->remember = $this->config->get("remember_Login");
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
}
?>
