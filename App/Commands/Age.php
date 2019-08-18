<?php

namespace App\Commands;

use App\TgHelpers\TelegramApi;

class Age extends BaseCommand {

	private $mode = 'age';

	function processCommand($par = false) {
		switch ($this->userData['mode']) {
			case $this->mode:
				$this->db->table('users')->where('chatId', $this->chatId)->update(['age' => $this->config['ageMap'][$this->tgParser->getMessage()]]);
				$this->triggerCommand(District::class);
			break;
			default:
				$this->db->table('users')->where('chatId', $this->chatId)->update(['mode' => $this->mode]);
				TelegramApi::sendMessageWithKeyboard($this->text['setAge'], $this->text['ageButtons']);
		}
	}

}