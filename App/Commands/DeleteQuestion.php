<?php

namespace App\Commands;

use App\TgHelpers\TelegramApi;

class DeleteQuestion extends BaseCommand {

	function processCommand($par = false) {
		TelegramApi::deleteMessage($this->tgParser::getMsgId());
		$this->db->table('questions')->where('id', $this->tgParser::getCallbackByKey('qId'))->delete();
		$this->triggerCommand(UserQuestions::class);
	}

}