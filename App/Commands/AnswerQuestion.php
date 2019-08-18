<?php

namespace App\Commands;

use App\TgHelpers\TelegramApi;

class AnswerQuestion extends BaseCommand {

	function processCommand($par = false) {
		if ($this->tgParser::getCallbackByKey('a') == 'yes') {
			$status = 'agreed';

			$senderData = $this->db->table('questions AS q')->where('id', $this->tgParser::getCallbackByKey('qId'))->
			select(['chatId', 'question', '(SELECT yesUpdate FROM users as U WHERE chatId = q.chatId) AS yesUpdate'])->results();
			if ($senderData[0]['yesUpdate']) {
				TelegramApi::sendMessage('<a href="tg://user?id='.$this->chatId.'">'.$this->text['user'].'</a>'.$this->text['agreed']."\n".
					$senderData[0]['question'], $senderData[0]['chatId']);
			}
		} else {
			$status = 'refused';
		}

		$this->db->table('questionQueue')->
		where('questionId', $this->tgParser::getCallbackByKey('qId'))->
		update(['status' => $status]);

		TelegramApi::deleteMessage($this->tgParser::getMsgId());
	}

}