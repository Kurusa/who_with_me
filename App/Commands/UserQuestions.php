<?php

namespace App\Commands;

use App\TgHelpers\TelegramApi;
use App\TgHelpers\TelegramKeyboard;

class UserQuestions extends BaseCommand {

	function processCommand($par = false) {
		$questionList = $this->db->table('questions')->where('chatId', $this->chatId)->select()->results();

		if ($questionList[0]['question']) {
			TelegramKeyboard::$columns    = 1;
			TelegramKeyboard::$list       = $questionList;
			TelegramKeyboard::$buttonText = 'question';
			TelegramKeyboard::$action     = 'info';
			TelegramKeyboard::$id         = 'id';
			TelegramKeyboard::build();

			TelegramApi::sendMessageWithInlineKeyboard($this->text['listInfo'], TelegramKeyboard::get());
		} else {
			TelegramApi::sendMessage($this->text['emptyList']);
		}
	}

}