<?php

namespace App\Commands\AddQuestion;

use App\Commands\BaseCommand;

class ShowProfile extends BaseCommand {

	private $mode = 'setQProfile';

	function processCommand($par = false) {
		if (!$this->tgParser::isGroup()) {
			if ($this->userData['mode'] == $this->mode) {
				if ($this->tgParser::getMessage() == $this->text['no']) {
					$this->db->table('questionList')->where('id', $this->userData['questionId'])->update(['showProfile' => 0]);
				}
				$this->triggerCommand(SetQuestionActivity::class);
			} else {
				$this->db->table('userList')->where('chatId', $this->chatId)->update(['mode' => $this->mode]);
				$this->tg->sendMessageWithKeyboard($this->text['showProfileQ'], [
					[$this->text['yes'], $this->text['no']], [
						$this->text['cancel']
					]
				]);
			}
		} else {
			$this->db->table('questionList')->where('id', $this->userData['questionId'])->update(['showProfile' => 0]);
			$this->triggerCommand(SetQuestionActivity::class);
		}
	}

}