<?php

namespace App\Commands;

class GetUpdates extends BaseCommand {

	function processCommand($par = false) {
		if ($this->tgParser::getCallbackByKey('a') == 'update') {
			$key    = $this->tgParser::getCallbackByKey('key');
			$value  = intval(!($this->tgParser::getCallbackByKey('v')));

			$this->db->table('userList')->
			where('chatId', $this->chatId)->
			update([$key => $value]);

			$this->tg->updateMessageKeyboard($this->tgParser::getMsgId(), $this->text['updatesType'], $this->buildButtons());
		} else {
			$this->tg->sendMessageWithInlineKeyboard($this->text['updatesType'], $this->buildButtons());
		}
	}

	private function buildButtons() {
		$userUpdatesData = $this->db->table('userList')->where('chatId', $this->chatId)->select(array_keys($this->config['updates']))->results();

		$buttons = [];
		foreach ($this->tgParser::isGroup() ? $this->config['groupUpdates'] : $this->config['updates'] as $key => $update) {
			$check = !($userUpdatesData[0][$key]) ? $this->text['delete'] : $this->text['check'];
			$buttons[] = [[
				'text' => $update . $check,
				'callback_data' => json_encode(['a' => 'update', 'key' => $key, 'v' => $userUpdatesData[0][$key]]),
			]];
		}
		return $buttons;
	}

}