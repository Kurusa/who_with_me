<?php

namespace App\Commands;

class Sex extends BaseCommand {

	private $mode = 'sex';

	function processCommand($par = false) {
		switch ($this->userData['mode']) {
			case $this->mode:
				if ($this->config['startSexMap'][$this->tgParser::getMessage()] !== null) {
					$this->db->table('userList')->where('chatId', $this->chatId)->update(['sex' => $this->config['startSexMap'][$this->tgParser::getMessage()]]);
					$this->triggerCommand(Age::class);
				} else {
					$this->tg->sendMessageWithKeyboard($this->text['setSex'], [[$this->text['girlEmoji'], $this->text['boyEmoji']]]);
				}
			break;
			default:
				$this->db->table('userList')->where('chatId', $this->chatId)->update(['mode' => $this->mode]);
				$this->tg->sendMessageWithKeyboard($this->text['setSex'], [[$this->text['girlEmoji'], $this->text['boyEmoji']]]);
		}
	}

}

