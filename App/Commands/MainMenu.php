<?php

namespace App\Commands;

class MainMenu extends BaseCommand {

	function processCommand($par = false) {
		$this->db->table('userList')->where('chatId', $this->chatId)->update(['mode' => 'done']);

		$userButtons = [
			[$this->text['addQuestion'], $this->text['myQuestions']],
			[$this->text['settings'], $this->text['feedback']]
		];

		$groupButtons = [
			[$this->text['addQuestion'], $this->text['myQuestions']],
			[$this->text['settings'], $this->text['feedback']]
		];

		$this->tg->sendMessageWithKeyboard($par ? $par : $this->text['mainMenu'],
			$this->tgParser::isGroup() ? $groupButtons : $userButtons);
	}

}