<?php

namespace App\Commands;

class RefreshQuestion extends BaseCommand {

	function processCommand($par = false) {
		$this->tg->deleteMessage($this->tgParser::getMsgId());
		$data = $this->db->table('questionList')->where('id', $this->tgParser::getCallbackByKey('qId'))->select()->results();
		$newUpTo = time() + ($data[0]['date'] - $data[0]['upTo']);
		$this->db->table('questionList')->where('id', $this->tgParser::getCallbackByKey('qId'))->update(['upTo' => $newUpTo, 'active' => 1]);
		$this->triggerCommand(QuestionInfo::class, $this->tgParser::getCallbackByKey('qId'));
	}

}