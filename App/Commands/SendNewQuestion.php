<?php

namespace App\Commands;

class SendNewQuestion extends BaseCommand {

	function processCommand($par = false) {
		$data = $this->db->query('
		SELECT 
		U.sex AS userSex, U.age AS userAge, U.chatId AS uChatId,
		Q.question AS qQuestion, Q.id AS qId, Q.showProfile AS qShowProfile
		FROM questionList AS Q
		INNER JOIN userList AS U ON Q.chatId = U.chatId  
		WHERE Q.district IN ('.$this->userData['district'].', "all")
		AND Q.age IN ('.$this->userData['sex'].', "all")
		AND Q.sex IN ('.$this->userData['age'].', "all")
		AND Q.chatId <> '.$this->chatId.'
		AND Q.done = 1
		AND Q.moderated = 1
		')->results();

		if (!empty($data)) {
			$this->tg->sendMessage($this->text['uHave'].$this->db->count().$this->text['newQuestions']);

			\App\TgHelpers\BuildQuestionMsg::$config = $this->config;
			\App\TgHelpers\BuildQuestionMsg::$text   = $this->text;

			foreach ( $data as $datum ) {
				$text = \App\TgHelpers\BuildQuestionMsg::buildMsg($datum);
				$this->tg->sendMessageWithInlineKeyboard($text[0], $text[1], $this->chatId);

				$this->db->table('questionQueue')->
				insert(['chatId' => $this->chatId,
				        'questionId' => $datum['qId'],
				        'status' => 'sent']);
			}
		}
	}

}