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
	
	public function isCustomUsername(string $username) : bool
	{
		$user = $this->db->query("SELECT * FROM xbox WHERE username = '$username';")->fetchArray(SQLITE3_ASSOC) ["username"];
		return is_string($user);
	}
	
	/** USERNAME **/
	public function setUsername(string $gamertag, string $username)
	{
		$stmt = $this->db->prepare("INSERT OR REPLACE INTO xbox (gamertag, username) VALUES (:gamertag, :username);");
		$stmt->bindValue(":gamertag", $gamertag);
		$stmt->bindValue(":username", $username);
		$stmt->execute();
	}
	
	public function getUsername(string $gamertag)
	{
		return $this->db->query("SELECT * FROM xbox WHERE gamertag = '$gamertag';")->fetchArray(SQLITE3_ASSOC) ["username"];
	}
	
	/** PASSWORD **/
	public function setPassword(string $username, string $password)
	{
		$stmt = $this->db->prepare("INSERT OR REPLACE INTO passwords (username, password) VALUES (:username, :password);");
		$stmt->bindValue(":username", $username);
		$stmt->bindValue(":password", hash("md5", $password));
		$stmt->execute();
	}
	
	public function getPassword(string $username)
	{
		return $this->db->query("SELECT * FROM passwords WHERE username = '$username';")->fetchArray(SQLITE3_ASSOC) ["password"];
	}
	
	/** DISCORD **/
	public function setDiscord(string $username, string $discord = null)
	{
		$stmt = $this->db->prepare("INSERT OR REPLACE INTO discords (username, discord) VALUES (:username, :discord);");
		$stmt->bindValue(":username", $username);
		$stmt->bindValue(":discord", $discord);
		$stmt->execute();
	}
	
	public function getDiscord(string $username)
	{
		return $this->db->query("SELECT * FROM discords WHERE username = '$username';")->fetchArray(SQLITE3_ASSOC) ["discord"];
	}
	
	/** IP ADDRESS **/
	public function setIpAddress(string $username, string $address)
	{
		$stmt = $this->db->prepare("INSERT OR REPLACE INTO ipaddress (username, ip) VALUES (:username, :ip);");
		$stmt->bindValue(":username", $username);
		$stmt->bindValue(":ip", $address);
		$stmt->execute();
	}
	
	public function getIpAddress(string $username)
	{
		return $this->db->query("SELECT * FROM ipaddress WHERE username = '$username';")->fetchArray(SQLITE3_ASSOC) ["ip"];
	}
	
}

?>
