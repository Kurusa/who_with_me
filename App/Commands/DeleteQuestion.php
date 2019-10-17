<?php

namespace App\Commands;

class DeleteQuestion extends BaseCommand {

	function processCommand($par = false) {
		$this->db->query('DELETE FROM questionList WHERE id = '.$this->tgParser::getCallbackByKey('qId'));
		$this->tg->deleteMessage($this->tgParser::getMsgId());
		$this->triggerCommand(QuestionList::class);
	}

}