<?php

namespace Codeh\CustomAuth;

use pocketmine\Server;

class leDataHandler{
    
    private $main;

    public function __construct(\Codeh\CustomAuth\leMain $core)
    {
		$this->main = $core;
    }
    
	public function init() : void
	{
		$this->db = new \SQLite3($this->main->getDataFolder() . "database.db");
		$this->db->exec("CREATE TABLE IF NOT EXISTS xbox (gamertag BLOB PRIMARY KEY COLLATE NOCASE, username BLOB);");
		$this->db->exec("CREATE TABLE IF NOT EXISTS passwords (username BLOB PRIMARY KEY COLLATE NOCASE, password BLOB);");
		$this->db->exec("CREATE TABLE IF NOT EXISTS discords (username BLOB PRIMARY KEY COLLATE NOCASE, discord BLOB);");
		$this->db->exec("CREATE TABLE IF NOT EXISTS ipaddress (username BLOB PRIMARY KEY COLLATE NOCASE, ip BLOB);");
	}
	
	public function isCustom(string $string) : bool
	{
		$result = $this->db->query("SELECT * FROM xbox WHERE username = '$string';");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return empty($result) == false;
	}
	
	/** USERNAME **/
	public function setUsername(string $gamertag, string $username)
	{
		$stmt = $this->db->prepare("INSERT OR REPLACE INTO xbox (gamertag, username) VALUES (:gamertag, :username);")
		->bindValue(":gamertag", $player->getName())
		->bindValue(":username", $username)
		->execute();
	}
	
	public function getUsername(string $gamertag)
	{
		return $this->db->query("SELECT * FROM xbox WHERE gamertag = '$gamertag';")->fetchArray(SQLITE3_ASSOC) ["username"];
	}
	
	/** PASSWORD **/
	public function setPassword(string $username, string $password)
	{
		$stmt = $this->db->prepare("INSERT OR REPLACE INTO passwords (username, password) VALUES (:username, :password);")
		->bindValue(":username", $username)
		->bindValue(":password", hash("md5", $password))
		->execute();
	}
	
	public function getPassword(string $username)
	{
		return $this->db->query("SELECT * FROM passwords WHERE username = '$username';")->fetchArray(SQLITE3_ASSOC) ["password"];
	}
	
	/** DISCORD **/
	public function setDiscord(string $username, string $discord = null)
	{
		$stmt = $this->db->prepare("INSERT OR REPLACE INTO discords (username, discord) VALUES (:username, :discord);")
		->bindValue(":username", $username)
		->bindValue(":discord", $discord)
		->execute();
	}
	
	public function getDiscord(string $username)
	{
		return $this->db->query("SELECT * FROM discords WHERE username = '$username';")->fetchArray(SQLITE3_ASSOC) ["discord"];
	}
	
	/** IP ADDRESS **/
	public function setIpAddress(string $username, string $address)
	{
		$this->db->prepare("INSERT OR REPLACE INTO ipaddress (username, ip) VALUES (:username, :ip);")
		->bindValue(":username", $username)
		->bindValue(":ip", $address)
		->execute();
	}
	
	public function getIpAddress(string $username)
	{
		return $this->db->query("SELECT * FROM ipaddress WHERE username = '$username';")->fetchArray(SQLITE3_ASSOC) ["ip"];
	}
	
}

?>
