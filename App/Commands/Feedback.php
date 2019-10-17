<?php

namespace App\Commands;

class Feedback extends BaseCommand {

	public $mode = 'feedback';

	function processCommand($par = false) {
		if ($this->userData['mode'] == $this->mode) {
			$this->db->table('userList')->where('chatId', $this->chatId)->update(['mode' => 'done']);
			$this->db->table('feedback')->insert(['chatId' => $this->tgParser::getChatId(), 'text' => $this->tgParser::getMessage(), 'date' => time()]);
			$this->triggerCommand(MainMenu::class, $this->text['msgSend']);
		} else {
			$this->db->table('userList')->where('chatId', $this->chatId)->update(['mode' => $this->mode]);
			$this->tg->sendMessageWithKeyboard($this->text['preSendFeedback'], [[$this->text['cancel']]]);
		}
	}

}