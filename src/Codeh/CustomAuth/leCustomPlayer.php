<?php
declare(strict_types = 1);

/**
 *  _   _                      _____                     ______
 * | \ | |                    /  ___|                    | ___ \
 * |  \| | __ _ _ __ ___   ___\ `--. _ __   __ _  ___ ___| |_/ /___ _ __ ___   _____   _____ _ __
 * | . ` |/ _` | '_ ` _ \ / _ \`--. \ '_ \ / _` |/ __/ _ \    // _ \ '_ ` _ \ / _ \ \ / / _ \ '__|
 * | |\  | (_| | | | | | |  __/\__/ / |_) | (_| | (_|  __/ |\ \  __/ | | | | | (_) \ V /  __/ |
 * \_| \_/\__,_|_| |_| |_|\___\____/| .__/ \__,_|\___\___\_| \_\___|_| |_| |_|\___/ \_/ \___|_|
 *                                  | |
 *                                  |_|
 *
 * NameSpaceRemover, a NameSpaceRemover plugin for PocketMine-MP
 * Copyright (c) 2018 JackMD  < https://github.com/JackMD >
 *
 * Discord: JackMD#3717
 * Twitter: JackMTaylor_
 *
 * This software is distributed under "GNU General Public License v3.0".
 * This license allows you to use it and/or modify it but you are not at
 * all allowed to sell this plugin at any cost. If found doing so the
 * necessary action required would be taken.
 *
 * NameSpaceRemover is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License v3.0 for more details.
 *
 * You should have received a copy of the GNU General Public License v3.0
 * along with this program. If not, see
 * <https://opensource.org/licenses/GPL-3.0>.
 * ------------------------------------------------------------------------
 */

//Codes from JackMD's NameSpaceRemover

namespace Codeh\CustomAuth;

use pocketmine\Player;
use pocketmine\Server;

class leCustomPlayer extends Player{

	public $isCustomized = false, $isLoggedIn;
	private $tries;

	public function getName(): string
	{
		return ($this->isCustomized) ? $this->username : $this->customize($this->username);
	}

	public function getDisplayName(): string
	{
		return ($this->isCustomized) ? $this->displayName : $this->customize($this->displayName);
	}

	public function getLowerCaseName(): string
	{
		return ($this->isCustomized) ? $this->iusername : strtolower($this->customize($this->iusername));
	}
	
	public function isLoggedIn() : bool
	{
		return $this->isLoggedIn;
	}
	
	public function getPlayerTries() : int
	{
		return $this->tries;
	}
	
	public function setPlayerTries(int $t) : void
	{
		$this->tries = $t;
	}
	
	private function customize(string $username) : string
	{
		$main = Server::getInstance()->getPluginManager()->getPlugin("CustomAuth");
		if(is_null($main->data->getUsername($username))) //this somehow works but not isCustomUsername
		{
			$this->isLoggedIn = false;
			return $username;
		} else {
			$customuser = $main->data->getUsername($username);
			$this->username = $customuser;
			$this->displayName = $customuser;
			$this->iusername = strtolower($customuser);
			$this->isCustomized = true;
			$this->tries = 5;
			$this->isLoggedIn = false;
			return $customuser;
		}
	}
}
