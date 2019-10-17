<?php

namespace App\Commands;

class AnswerQuestion extends BaseCommand {

	function processCommand($par = false) {
		if ($this->tgParser::getCallbackByKey('a') == 'yes') {
			$status = 'agreed';

			$senderData = $this->db->table('questionList AS q')->where('id', $this->tgParser::getCallbackByKey('qId'))->
			select(['chatId', 'question', '(SELECT yesUpdate FROM userList as U WHERE chatId = q.chatId) AS yesUpdate'])->results();
			if ($senderData[0]['yesUpdate']) {
				$this->tg->sendMessage('<a href="tg://user?id='.$this->chatId.'">'.$this->text['user'].'</a>'.$this->text['agreed']."\n".
					$senderData[0]['question'], $senderData[0]['chatId']);
			}
		} else {
			$status = 'refused';
		}

		$this->db->table('questionQueue')->
		where('questionId', $this->tgParser::getCallbackByKey('qId'))->
		where('chatId', $this->chatId)->
		update(['status' => $status]);

		$this->tg->deleteMessage($this->tgParser::getMsgId());
	}

}