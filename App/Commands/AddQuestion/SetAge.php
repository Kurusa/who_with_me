<?php

namespace App\Commands\AddQuestion;

use App\Commands\BaseCommand;
use App\TgHelpers\TelegramApi;

class SetAge extends BaseCommand {

	private $mode = 'setQAge';

	function processCommand($par = false) {
		if ($this->userData['mode'] == $this->mode) {
			$age = $this->config['ageMap'][$this->tgParser::getMessage()];
			$this->db->table('questions')->where('id', $this->userData['questionId'])->update(['age' => $age]);
			$this->triggerCommand(SetDistrict::class);
		} else {
			$this->db->table('users')->where('chatId', $this->chatId)->update(['mode' => $this->mode]);
			$this->text['ageButtons'][] = [$this->text['ageAll']];
			$this->text['ageButtons'][] = [$this->text['cancel']];
			TelegramApi::sendMessageWithKeyboard($this->text['sendQAge'], $this->text['ageButtons']);
		}
	}

}