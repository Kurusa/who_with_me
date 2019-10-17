<?php

namespace App\Commands\AddQuestion;

use App\Commands\BaseCommand;
use App\Commands\Unknown;

class SetAge extends BaseCommand {

	private $mode = 'setQAge';

	function processCommand($par = false) {
		if ($this->userData['mode'] == $this->mode) {
			$age = $this->config['ageMap'][$this->tgParser::getMessage()];
			if ($age) {
				$this->db->table('questionList')->where('id', $this->userData['questionId'])->update(['age' => $age]);
				$this->triggerCommand(SetDistrict::class);
			} else {
				$this->triggerCommand(Unknown::class);
			}
		} else {
			$this->db->table('userList')->where('chatId', $this->chatId)->update(['mode' => $this->mode]);
			$this->text['ageButtons'][] = [$this->text['ageAll']];
			$this->text['ageButtons'][] = [$this->text['cancel']];
			$this->tg->sendMessageWithKeyboard($this->text['sendQAge'], $this->text['ageButtons']);
		}
	}

}