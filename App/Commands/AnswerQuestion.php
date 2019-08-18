<?php

namespace App\Commands;

use App\TgHelpers\TelegramApi;

class AnswerQuestion extends BaseCommand {

	function processCommand($par = false) {
		//$this->db->table('seen')->insert(['questionId' => $this->tgParser::getCallbackByKey('qId'), 'chatId' => $this->chatId]);

		if ($this->tgParser::getCallbackByKey('a') == 'yes') {
			$this->db->table('agrees')->insert(['questionId' => $this->userData['questionId'], 'chatId' => $this->chatId]);
			$senderData = $this->db->table('questions AS q')->where('id', $this->userData['questionId'])->
			select(['chatId', 'question', '(SELECT yesUpdate FROM users as U WHERE chatId = q.chatId) AS yesUpdate'])->results();
			if ($senderData[0]['yesUpdate']) {
				TelegramApi::sendMessage('<a href="tg://user?id='.$this->chatId.'">'.$this->text['user'].'</a>'.$this->text['agreed']."\n".
					$senderData[0]['question'], $senderData[0]['chatId']);
			}
		}

		TelegramApi::deleteMessage($this->tgParser::getMsgId());
	}

}