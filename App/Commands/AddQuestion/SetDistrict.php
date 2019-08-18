<?php

namespace App\Commands\AddQuestion;

use App\Commands\BaseCommand;
use App\TgHelpers\TelegramApi;
use App\TgHelpers\TelegramKeyboard;

class SetDistrict extends BaseCommand {

	private $mode = 'setQDist';

	function processCommand($par = false) {
		if ($this->userData['mode'] == $this->mode) {
			switch ($this->tgParser::getCallbackByKey('a')) {
				case 'distDoneQ':
					$this->db->table('questions')->where('id', $this->userData['questionId'])->update(['district' => $this->tgParser::getCallbackByKey('id')]);
					$districtName = $this->db->table('districtList')->where('id', $this->tgParser::getCallbackByKey('id'))->select(['district'])->results();
					TelegramApi::sendMessage($this->text['uSelectedDist'].$districtName[0]['district']);
					$this->triggerCommand(ShowProfile::class);
				break;
				case 'nextDistQ':
				case 'prevDistQ':
					TelegramApi::updateMessageKeyboard($this->tgParser::getMsgId(), $this->text['list'], $this->buildKeyboard());
				break;
			}

			switch ($this->tgParser::getMessage()) {
				case $this->text['allDist']:
					$this->db->table('questions')->where('id', $this->userData['questionId'])->update(['district' => 'all']);
					TelegramApi::deleteMessage($this->tgParser::getMsgId());
					$this->triggerCommand(ShowProfile::class);
				break;
				case $this->text['fromList']:
					TelegramApi::deleteMessage($this->tgParser::getMsgId());
					TelegramApi::sendMessageWithKeyboard($this->text['selectDistrictQ'], [[$this->text['cancel']]]);
					TelegramApi::sendMessageWithInlineKeyboard($this->text['list'], $this->buildKeyboard());
				break;
			}
		} else {
			$this->db->table('users')->where('chatId', $this->chatId)->update(['mode' => $this->mode]);
			TelegramApi::sendMessageWithKeyboard($this->text['sendQDist'], [
					[$this->text['fromList']],
					[$this->text['allDist']],
					[$this->text['cancel']]
			]);
		}
	}

	function buildKeyboard() {
		$nextPage = $this->tgParser::getCallbackByKey('a') == 'nextDistQ' ? false : true;

		$districtList = $this->db->table('districtList')->
		limit(12)->offset($nextPage ? 0 : 12)->
		select(['district AS name', 'id'])->results();

		TelegramKeyboard::$columns    = 2;
		TelegramKeyboard::$list       = $districtList;
		TelegramKeyboard::$buttonText = 'name';
		TelegramKeyboard::$action     = 'distDoneQ';
		TelegramKeyboard::$id         = 'id';
		TelegramKeyboard::build();
		TelegramKeyboard::addButton($nextPage ? '>' : '<', ['a' => $nextPage ? 'nextDistQ' : 'prevDistQ']);

		return TelegramKeyboard::get();
	}

}