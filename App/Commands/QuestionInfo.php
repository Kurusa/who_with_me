<?php

namespace App\Commands;

class QuestionInfo extends BaseCommand {

	private $msg;
	private $buttons;
	private $questionId;

	function processCommand($par = false) {
		$this->questionId = $par ? $par : $this->tgParser->getCallbackByKey('id');

		$questionData = $this->db->table('questionList')->
		where('id', $this->questionId)->select(['question', 'active', 'moderated', 'upTo'])->
		results();

		$this->msg .= "<b>".$questionData[0]['question']."</b>"."\n"."\n";
		$this->agreesText();
		$this->seenText();
		$this->upTo($questionData[0]['active'], $questionData[0]['upTo']);
		$this->moderated($questionData[0]['moderated']);
		$this->buildButtons($questionData[0]['active']);

		$this->tg->sendMessageWithInlineKeyboard($this->msg, $this->buttons);
	}

	private function buildButtons($status) {
		$this->buttons = [
			[
				[
					'text' => ($status === '1') ? $this->text['active'] : $this->text['refresh'] ,
					'callback_data' => json_encode(['a' => $status ? 'a' : 'refresh', 'qId' => $this->questionId]),
				], [
					'text' => $this->text['deleteQ'],
					'callback_data' => json_encode(['a' => 'deleteQuestion', 'qId' => $this->questionId]),
				],
			],
		];
	}

	private function agreesText() {
		$agrees = $this->db->table('questionQueue as Q')->
		where('questionId', $this->questionId)->
		where('status', 'agreed')->
		select(['chatId', '(SELECT userName FROM userList AS U WHERE U.chatId = Q.chatId) AS userName'])->results();

		$this->msg .= $this->text['agrees'];
		if (!empty($agrees)) {
			$this->msg .= "\n";
			foreach ( $agrees as $value ) {
				$name = $value['userName'] ? $value['userName'] : $this->text['profile'];
				$this->msg .= '<a href="tg://user?id='.$value['chatId'].'">'.$name.'</a>'."\n";
			}
		} else {
			$this->msg .= '0'."\n";
		}
	}

	private function seenText() {
		$seen = $this->db->table('questionQueue')->
		where('questionId', $this->questionId)->
		addToQuery(" AND status IN ('sent', 'agreed', 'refused')")->
		select(['COUNT(*) AS count'])->results();

		$this->msg .= "\n".$this->text['seenAmount'].$seen[0]['count']."\n";
	}

	private function upTo($status, $upTo) {
		$this->msg .= "\n".($status ? $this->text['upTo'] : $this->text['wasUpTo']).date('Y-m-d H:i', $upTo);
		if (!$status) {
			$this->msg .= "\n".$this->text['refreshHelp'].$this->text['refresh'];
		}
	}

	private function moderated($status) {
		$this->msg .= "\n"."\n";
		$this->msg .= ($status === '1') ? $this->text['moderated'] : $this->text['notModerated'];
	}

}