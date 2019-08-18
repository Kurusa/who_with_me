<?php

namespace App\Commands\AddQuestion;

use App\Commands\BaseCommand;
use App\Commands\MainMenu;
use App\TgHelpers\TelegramApi;

class SetText extends BaseCommand {

	private $mode = 'setQText';

	function processCommand($par = false) {
		$activeUserQuestions = $this->db->table('questions')->
		where('chatId', $this->chatId)->
		where('active', '1')->
		where('moderated', '1')->
		select(['COUNT(*) AS count'])->results();

		if ($activeUserQuestions[0]['count'] < 3) {
			if ($this->userData['mode'] == $this->mode) {
				$text = trim($this->tgParser::getMessage());
				if ((strlen($text) > 5) && (strlen($text) < 1000)) {
					$this->db->table('questions')->where('id', $this->userData['questionId'])->update(['question' => $text]);
					$this->triggerCommand(SetSex::class);
				} else {
					TelegramApi::sendMessage($this->text['wrongLength']);
				}
			} else {
				$this->db->table('users')->where('chatId', $this->chatId)->update(['mode' => $this->mode]);
				TelegramApi::sendMessageWithKeyboard($this->text['sendQText'], [[$this->text['cancel']]]);
			}
		} else {
			$this->triggerCommand(MainMenu::class, $this->text['tooMuchQuestions']);
		}
	}

}