<?php

namespace App\Commands\AddQuestion;

use App\Commands\BaseCommand;
use App\TgHelpers\TelegramApi;

class SetSex extends BaseCommand {

	private $mode = 'setQSex';

	function processCommand($par = false) {
		if ($this->userData['mode'] == $this->mode) {
			$text = $this->tgParser::getMessage();
			$this->db->table('questions')->where('id', $this->userData['questionId'])->update(['sex' => $this->config['sexQMap'][$text]]);
			$this->triggerCommand(SetAge::class);
		} else {
			$this->db->table('users')->where('chatId', $this->chatId)->update(['mode' => $this->mode]);
			TelegramApi::sendMessageWithKeyboard($this->text['sendQSex'], [
					[$this->text['girlQEmoji'], $this->text['boyQEmoji']],
					[$this->text['allOfThem']],
					[$this->text['cancel']
				]]);
		}
	}

}