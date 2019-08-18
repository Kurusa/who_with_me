<?php

namespace App\Commands\AddQuestion;

use App\Commands\BaseCommand;
use App\TgHelpers\TelegramApi;

class ModerateQuestion extends BaseCommand {

	function processCommand($par = false) {
		if ($this->tgParser::getCallbackByKey('a') == 'moderate') {
			if ($this->tgParser::getCallbackByKey('qId')) {
				$this->db->table('questions')->where('id', $this->tgParser::getCallbackByKey('qId'))->update(['moderated' => '1']);
			}
			TelegramApi::deleteMessage($this->tgParser::getMsgId());
		} else {
			$questionData = $this->db->table('questions')->where('id', $this->userData['questionId'])->select()->results();

			$text = "<b>Модерація</b>"."\n".$questionData[0]['question']."\n";
			$text .= 'Від '.$this->userData['userName'];
			$buttons = [
				[
					[
						'text' => 'так', 'callback_data' => json_encode(['a' => 'moderate', 'qId' => $questionData[0]['id']]),
					], [
						'text' => 'ні', 'callback_data' => json_encode(['a' => 'moderate']),
					],
				],
			];

			TelegramApi::sendMessageWithInlineKeyboard($text, $buttons, 375036391);
			$this->db->table('users')->where('chatId', $this->chatId)->update(['questionId' => '0']);
		}
	}

}