<?php

namespace Codeh\CustomAuth;

use pocketmine\Server;
use pocketmine\scheduler\Task;

class leTask extends Task{
    
    private $main;

    public function __construct(\Codeh\CustomAuth\leMain $core)
    {
		$this->main = $core;
    }
    
	public function onRun(int $currentTick)
	{
		$stayReq = $this->main->grace;
		$ctime = $this->main->microtime_int();
		foreach($this->main->notLoggedIn as $name => $ptime)
		{
			$hooman = $this->main->getServer()->getPlayer($name);
			$stayed = $ptime - $ctime; //check how long the player is online (in seconds).
			if(($stayed / 60) >= $stayReq) //converts that in minute then check if that is enough.
			{
				if($hooman instanceof leCustomPlayer) $hooman->kick("§lYou have exceeded the allowed time to login.");
			} else {
				if($hooman instanceof leCustomPlayer) $hooman->sendMessage($this->main->customAuthName . " §lPlease login [/login] to use the server features");
			}
		}
	}
}

?>
