<?php

namespace App\Commands;

use App\TgHelpers\TelegramApi;

class Feedback extends BaseCommand {

	public $mode = 'feedback';

	function processCommand($par = false) {
		if ($this->userData['mode'] == $this->mode) {
			$this->db->table('users')->where('chatId', $this->chatId)->update(['mode' => 'done']);
			$this->db->table('feedback')->insert(['chatId' => $this->tgParser::getChatId(), 'text' => $this->tgParser::getMessage(), 'date' => time()]);
			$this->triggerCommand(MainMenu::class, $this->text['msgSend']);
		} else {
			$this->db->table('users')->where('chatId', $this->chatId)->update(['mode' => $this->mode]);
			TelegramApi::sendMessageWithKeyboard($this->text['preSendFeedback'], [[$this->text['cancel']]]);
		}
	}

}