<?php

declare(strict_types = 1);

namespace Codeh\CustomAuth;

use pocketmine\utils\TextFormat as T;
use pocketmine\Player;

use Codeh\FormAPIbyJojoe\{SimpleForm, ModalForm, CustomForm};

class leForms{
	
    public function __construct(\Codeh\CustomAuth\leMain $core)
    {
		$this->main = $core;
    }

	public function loginForm($player)
	{
		$form = new CustomForm(function (Player $player, $data){
            if ($data === null){
                return;
            } else {
               $this->main->processLogin($player, T::clean($data[1]));
            }
            return true;
        });
        $form->setTitle($this->main->customAuthName);
        $form->addLabel(T::BOLD . T::WHITE . "Username: " . T::YELLOW . $player->getName() . T::EOL . T::WHITE ."Attempt(s): " . T::GREEN . $player->getPlayerTries());
		$form->addInput(T::BOLD . T::WHITE . "Password:");
		$form->sendToPlayer($player);
	}
	
	public function registrationForm($player)
	{
		$form = new CustomForm(function (Player $player, $data){
            if ($data === null){
                return;
            } else {
				if(strlen($data[1]) > 15) return $player->sendMessage($this->main->customAuthName . T::RED . " Username exceeds the limit!");
				if(strlen($data[2]) < $this->main->minChar) return $player->sendMessage($this->main->customAuthName . T::RED . " Password is less than " . $this->main->minChar);
				if(strlen($data[2]) > $this->main->maxChar) return $player->sendMessage($this->main->customAuthName . T::RED . " Password is more than " . $this->main->maxChar);
				$this->main->processRegistration($player, $data);
            }
            return true;
        });
        $form->setTitle($this->main->customAuthName);
        $form->addLabel(
		T::BOLD . T::WHITE . "[Guidelines to follow]" . T::EOL .
		T::YELLOW . "Max Username Char:" . T::WHITE . " 15" . T::EOL .
		T::YELLOW . "Min Password Char: " . T::WHITE . $this->main->minChar . T::EOL .
		T::YELLOW . "Max Password Char: " . T::WHITE . $this->main->maxChar . T::EOL .
		T::GOLD . "=[ Infos only you can see ]=" . T::EOL .
		T::YELLOW . "Gamertag: " . $player->getName() . T::EOL .
		T::YELLOW . "IP Address: " . $player->getAddress() . T::EOL
		);
		$form->addInput(T::BOLD . T::WHITE . "Username:");
		$form->addInput(T::BOLD . T::WHITE . "Password: (case-sensitive)");
		$form->addInput(T::BOLD . T::WHITE . "Discord (leave if none):");
		$form->sendToPlayer($player);
	}

}
