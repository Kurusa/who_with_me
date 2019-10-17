<?php

namespace App\Commands;

use App\TgHelpers\TelegramKeyboard;

class District extends BaseCommand {

	private $mode       = 'district';
	private $setMode    = 'setDistrict';

	function processCommand($par = false) {
		if ($this->tgParser::getCallbackByKey('a') == 'distDone') {
			$this->triggerCommand(InviteMsg::class, true);

			$this->db->table('userList')->where('chatId', $this->chatId)->update(['mode' => 'done', 'district' => $this->tgParser::getCallbackByKey('id')]);
			$this->tg->deleteMessage($this->tgParser::getMsgId());

			$districtName = $this->db->table('districtList')->where('id', $this->tgParser::getCallbackByKey('id'))->select(['district'])->results();
			$this->triggerCommand(MainMenu::class, $this->text['uSelectedDist'].$districtName[0]['district']);

			if ($this->userData['mode'] == $this->mode) {
				$this->triggerCommand(MainMenu::class, $this->tgParser::isGroup() ? $this->text['readyGroup'] : $this->text['ready'] );
				$this->triggerCommand(InviteMsg::class);
				if (!$this->tgParser::isGroup()) {
					$this->triggerCommand(SendNewQuestion::class, $this->text['ready']);
				}
			}
			exit;
		}

		$mode = ($this->tgParser::getMessage() == $this->text['changeDistrict']) ? $this->setMode : $this->mode;
		$this->db->table('userList')->where('chatId', $this->chatId)->update(['mode' => $mode]);

		if ($this->tgParser::getCallbackByKey('a') == 'nextDist' || $this->tgParser::getCallbackByKey('a') == 'prevDist') {
			$this->tg->updateMessageKeyboard($this->tgParser::getMsgId(), $this->text['list'], $this->buildKeyboard());
		} else {
			if ($mode == $this->setMode) {
				$this->tg->sendMessageWithKeyboard($this->text['selectDistrict'], [[$this->text['cancel']]]);
			} else {
				$this->tg->removeKeyboard($this->text['selectDistrict']);
			}
			$this->tg->sendMessageWithInlineKeyboard($this->text['list'], $this->buildKeyboard());
		}
	}

	function buildKeyboard() {
		$nextPage = $this->tgParser::getCallbackByKey('a') == 'nextDist' ? false : true;

		$districtList = $this->db->table('districtList')->
		limit(12)->offset($nextPage ? 0 : 12)->
		select(['district AS name', 'id'])->results();

		TelegramKeyboard::$columns    = 2;
		TelegramKeyboard::$list       = $districtList;
		TelegramKeyboard::$buttonText = 'name';
		TelegramKeyboard::$action     = 'distDone';
		TelegramKeyboard::$id         = 'id';
		TelegramKeyboard::build();
		TelegramKeyboard::addButton($nextPage ? '>' : '<', ['a' => $nextPage ? 'nextDist' : 'prevDist']);
		return TelegramKeyboard::get();
	}

}