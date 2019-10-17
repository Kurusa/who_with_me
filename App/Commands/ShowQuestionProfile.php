<?php

namespace App\Commands;

class ShowQuestionProfile extends BaseCommand {

	function processCommand($par = false) {
		$question = $this->db->table('questionList')->where('id', $this->tgParser::getCallbackByKey('qId'))->select()->results();

		$user = $this->db->table('userList')->where('chatId', $question[0]['chatId'])->select()->results();
		$flippedAge = array_flip($this->config['ageMap']);
		$text = $question[0]['question']."\n";
		$text .= $this->text['questionFrom'].
			$this->config['sexMap'][$user[0]['sex']].', '.
			$flippedAge[$user[0]['age']].$this->text['ages']."\n";
		$text .= "\n".'<a href="tg://user?id='.$question[0]['chatId'].'">'.$this->text['profile'].'</a>';

		$buttons = [
			[
				[
					'text' => $this->text['yes'], 'callback_data' => json_encode(['a' => 'yes', 'qId' => $question[0]['id'],]),
				], [
					'text' => $this->text['no'], 'callback_data' => json_encode(['a' => 'no', 'qId' => $question[0]['id']]),
				],
			],
		];

		$this->tg->updateMessageKeyboard($this->tgParser::getMsgId(), $text, $buttons);
		$this->tg->sendMessage('<a href="tg://user?id='.$this->chatId.'">'.$this->text['user'].'</a>'.$this->text['sawProfile'], $question[0]['chatId']);
	}

}