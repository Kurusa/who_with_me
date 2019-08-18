<?php

namespace App\Commands;

use App\TgHelpers\TelegramApi;

class GetUpdates extends BaseCommand {

	function processCommand($par = false) {
		if ($this->tgParser::getCallbackByKey('a') == 'update') {
			$key    = $this->tgParser::getCallbackByKey('key');
			$value  = !($this->tgParser::getCallbackByKey('v'));

			$this->db->table('users')->
			where('chatId', $this->chatId)->
			update([$key => $value]);

			TelegramApi::updateMessageKeyboard($this->tgParser::getMsgId(), $this->text['updatesType'], $this->buildButtons());
		} else {
			TelegramApi::sendMessageWithInlineKeyboard($this->text['updatesType'], $this->buildButtons());
		}
	}

	private function buildButtons() {
		$userUpdatesData = $this->db->table('users')->where('chatId', $this->chatId)->select(array_keys($this->config['updates']))->results();

		$buttons = [];
		foreach ($this->config['updates'] as $key => $update) {
			$check = !($userUpdatesData[0][$key]) ? $this->text['delete'] : $this->text['check'];
			$buttons[] = [[
				'text' => $update . $check,
				'callback_data' => json_encode(['a' => 'update', 'key' => $key, 'v' => $userUpdatesData[0][$key]]),
			]];
		}
		return $buttons;
	}

}