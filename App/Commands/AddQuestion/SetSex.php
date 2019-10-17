<?php

namespace App\Commands\AddQuestion;

use App\Commands\BaseCommand;

class SetSex extends BaseCommand {

	private $mode = 'setQSex';

	function processCommand($par = false) {
		if ($this->userData['mode'] == $this->mode) {
			$text = $this->tgParser::getMessage();
			$this->db->table('questionList')->where('id', $this->userData['questionId'])->update(['sex' => $this->config['sexQMap'][$text]]);
			$this->triggerCommand(SetAge::class);
		} else {
			$this->db->table('userList')->where('chatId', $this->chatId)->update(['mode' => $this->mode]);
			$this->tg->sendMessageWithKeyboard($this->text['sendQSex'], [
					[$this->text['girlQEmoji'], $this->text['boyQEmoji']],
					[$this->text['allOfThem']],
					[$this->text['cancel']
				]]);
		}
	}

}