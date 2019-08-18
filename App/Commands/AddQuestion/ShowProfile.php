<?php

namespace App\Commands\AddQuestion;

use App\Commands\BaseCommand;
use App\TgHelpers\TelegramApi;

class ShowProfile extends BaseCommand {

	private $mode = 'setQProfile';

	function processCommand($par = false) {
		if ($this->userData['mode'] == $this->mode) {
			if ($this->tgParser::getMessage() == $this->text['no']) {
				$this->db->table('questions')->where('id', $this->userData['questionId'])->update(['showProfile' => 0]);
			}
			$this->triggerCommand(SetQuestionActivity::class);
		} else {
			$this->db->table('users')->where('chatId', $this->chatId)->update(['mode' => $this->mode]);
			TelegramApi::sendMessageWithKeyboard($this->text['showProfileQ'], [
					[$this->text['yes'], $this->text['no']],
					[$this->text['cancel']
				]]);
		}
	}

}