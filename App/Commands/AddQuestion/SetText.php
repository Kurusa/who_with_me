<?php

namespace App\Commands\AddQuestion;

use App\Commands\BaseCommand;
use App\Commands\MainMenu;

class SetText extends BaseCommand {

	private $mode = 'setQText';

	function processCommand($par = false) {

		if ($this->checkLimit() === true) {
			if ($this->userData['mode'] == $this->mode) {
				$text = trim($this->tgParser::getMessage());
				if ((strlen($text) > 5) && (strlen($text) < 1000)) {
					$this->db->table('questionList')->where('id', $this->userData['questionId'])->update(['question' => $text]);
					$this->triggerCommand(SetSex::class);
				} else {
					$this->tg->sendMessage($this->text['wrongLength']);
				}
			} else {
				$this->db->table('questionList')->insert(['chatId' => $this->chatId, 'date' => time()]);
				$this->db->table('userList')->where('chatId', $this->chatId)->update(['questionId' => $this->db->lastInsertedId()]);

				$this->db->table('userList')->where('chatId', $this->chatId)->update(['mode' => $this->mode]);
				$this->tg->sendMessageWithKeyboard($this->text['sendQText'], [[$this->text['cancel']]]);
			}
		} else {
			$this->triggerCommand(MainMenu::class, $this->text['tooMuchQuestions']."\n".$this->text['yourLimit'].$this->checkLimit());
		}
	}

	private function checkLimit() {
		$limit = 3;

		$this->db->table('inviteList')->where('inviterChatId', $this->chatId)->select()->results();
		if ($this->db->count()) {
			$limit += $this->db->count();
		}

		$activeUserQuestions = $this->db->table('questionList')->
		where('chatId', $this->chatId)->
		where('active', '1')->
		where('moderated', '1')->
		select(['COUNT(*) AS count'])->results();

		if ($activeUserQuestions[0]['count'] < $limit) {
			return true;
		} else {
			return $limit;
		}
	}

}