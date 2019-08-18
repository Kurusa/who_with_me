<?php

namespace App\Commands;

use App\TgHelpers\TelegramApi;

class Start extends BaseCommand {

	function processCommand($par = false) {
		if (!empty($this->userData)) {
			$this->triggerCommand(MainMenu::class);
		} else {
			$this->db->table('users')->insert(['chatId' => $this->tgParser::getChatId(), 'userName' => $this->tgParser::getUserName(), 'date' => time(),]);
			TelegramApi::sendMessage($this->text['hello']);
			$this->triggerCommand(Sex::class);
		}
	}

}

