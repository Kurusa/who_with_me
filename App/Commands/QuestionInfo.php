<?php

namespace App\Commands;

use App\TgHelpers\TelegramApi;

class QuestionInfo extends BaseCommand {

	private $msg;
	private $buttons;
	private $questionId;

	function processCommand($par = false) {
		$this->questionId = $par ? $par : $this->tgParser->getCallbackByKey('id');

		$questionData = $this->db->table('questions')->
		select(['active'])->results();

		$this->agreesText();
		$this->seenText();
		$this->upTo($questionData[0]['active']);
		$this->buildButtons($questionData[0]['active']);

		TelegramApi::sendMessageWithInlineKeyboard($this->msg, $this->buttons);
	}

	private function buildButtons($status) {
		$this->buttons = [
			[
				[
					'text' => $status ? $this->text['active'] : $this->text['refresh'] ,
					'callback_data' => json_encode(['a' => $status ? 'a' : 'refresh', 'qId' => $this->questionId]),
				], [
					'text' => $this->text['deleteQ'],
					'callback_data' => json_encode(['a' => 'deleteQuestion', 'qId' => $this->questionId]),
				],
			],
		];
	}

	private function agreesText() {
		$agrees = $this->db->table('agrees AS A')->where('questionId', $this->questionId)->
		select(['COUNT(*) as count ', 'chatId', '(SELECT userName FROM users AS U WHERE U.chatId = A.chatId) AS userName'])->results();

		$this->msg .= $this->text['agrees']."\n";
		foreach ( $agrees as $value ) {
			$name = $value['userName'] ? $value['userName'] : $this->text['profile'];
			$this->msg .= '<a href="tg://user?id='.$value['chatId'].'">'.$name.'</a>';
		}
	}

	private function seenText() {
		$seen = $this->db->table('questionQueue')->
		where('questionId', $this->questionId)->
		select(['COUNT(*) AS count'])->results();

		$this->msg .= "\n"."\n".$this->text['seenAmount'].$seen[0]['count']."\n";
	}

	private function upTo($status) {
		$data = $this->db->table('questions')->where('id', $this->questionId)->select(['upTo'])->results();

		$this->msg .= "\n".($status ? $this->text['upTo'] : $this->text['wasUpTo']).date('Y-m-d H:i', $data[0]['upTo']);
		if (!$status) {
			$this->msg .= "\n".$this->text['refreshHelp'].$this->text['refresh'];
		}
	}

	private function modarated($status) {
		$data = $this->db->table('questions')->where('id', $this->questionId)->select(['upTo'])->results();

		$this->msg .= "\n".($status ? $this->text['upTo'] : $this->text['wasUpTo']).date('Y-m-d H:i', $data[0]['upTo']);
		if (!$status) {
			$this->msg .= "\n".$this->text['refreshHelp'].$this->text['refresh'];
		}
	}

}