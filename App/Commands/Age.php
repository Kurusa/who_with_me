<?php

namespace App\Commands;

class Age extends BaseCommand {

	private $mode = 'age';

	function processCommand($par = false) {
		switch ($this->userData['mode']) {
			case $this->mode:
				if ($this->config['ageMap'][$this->tgParser->getMessage()]) {
					$this->db->table('userList')->where('chatId', $this->chatId)->update(['age' => $this->config['ageMap'][$this->tgParser->getMessage()]]);
					$this->triggerCommand(District::class);
				} else {
					$this->tg->sendMessageWithKeyboard($this->text['setAge'], $this->text['ageButtons']);
				}
			break;
			default:
				$this->db->table('userList')->where('chatId', $this->chatId)->update(['mode' => $this->mode]);
				$this->tg->sendMessageWithKeyboard($this->text['setAge'], $this->text['ageButtons']);
		}
	}

}