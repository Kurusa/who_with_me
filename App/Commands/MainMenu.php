<?php

namespace App\Commands;

use App\TgHelpers\TelegramApi;

class MainMenu extends BaseCommand {

	function processCommand($par = false) {
		$this->db->table('users')->where('chatId', $this->chatId)->update(['mode' => 'done']);
		TelegramApi::sendMessageWithKeyboard($par ? $par : $this->text['mainMenu'], [
				[$this->text['addQuestion'], $this->text['myQuestions']],
				[$this->text['settings'], $this->text['feedback']]
		]);
	}

}