<?php

namespace App\Commands;

use App\TgHelpers\TelegramApi;

class Sex extends BaseCommand {

	private $mode = 'sex';

	function processCommand($par = false) {
		switch ($this->userData['mode']) {
			case $this->mode:
				$this->db->table('users')->where('chatId', $this->chatId)->update(['sex' => $this->config['startSexMap'][$this->tgParser::getMessage()]]);
				$this->triggerCommand(Age::class);
			break;
			default:
				$this->db->table('users')->where('chatId', $this->chatId)->update(['mode' => $this->mode]);
				TelegramApi::sendMessageWithKeyboard($this->text['setSex'], [[$this->text['girlEmoji'], $this->text['boyEmoji']]]);
		}
	}

}

