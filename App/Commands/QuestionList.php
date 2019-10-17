<?php

namespace App\Commands;

use App\TgHelpers\TelegramKeyboard;

class QuestionList extends BaseCommand {

	function processCommand($par = false) {
		$keyboard = $this->buildKeyboard();
		if ($keyboard) {
			$update = false;
			if ($this->tgParser::getCallbackByKey('a') == 'qPage') {
				$update = true;
			}

			if ($update) {
				$this->tg->updateMessageKeyboard($this->tgParser::getMsgId(), $this->text['listInfo'], $keyboard);
			} else {
				$this->tg->sendMessageWithInlineKeyboard($this->text['listInfo'], $keyboard);
			}
		} else {
			$this->tg->sendMessage($this->text['emptyList']);
		}
	}

	private function buildKeyboard() {
		$offset = $this->tgParser::getCallbackByKey('o');

		$questionList = $this->db->table('questionList')->
		where('chatId', $this->chatId)->
		limit(8)->
		offset($offset ? $offset : 0)->
		select()->results();
		$count = $this->db->count();

		if ($count) {
			TelegramKeyboard::$columns      = 2;
			TelegramKeyboard::$list         = $questionList;
			TelegramKeyboard::$buttonText   = 'question';
			TelegramKeyboard::$action       = 'info';
			TelegramKeyboard::$id           = 'id';
			TelegramKeyboard::build();

			$this->db->table('questionList')->
			where('chatId', $this->chatId)->
			limit(8)->
			offset($offset ? $offset + 12 : 12)->
			select()->results();

			if ($offset > 0) {
				TelegramKeyboard::addButton('<', ['a' => 'qPage', 'o' => $offset - 12]);
				if ($this->db->count()) {
					TelegramKeyboard::addButton('>', ['a' => 'qPage', 'o' => $offset + 12]);
				}
			} elseif ($count >= 8) {
				TelegramKeyboard::addButton('>', ['a' => 'qPage', 'o' => $offset + 12]);
			}

			return TelegramKeyboard::get();
		} else {
			return false;
		}
	}

}