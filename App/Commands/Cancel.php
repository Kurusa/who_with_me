<?php

namespace App\Commands;

class Cancel extends BaseCommand {

	function processCommand($par = false) {
		switch ($this->userData['mode']) {
			case 'setDistrict':
			case 'feedback':
				$this->db->table('users')->where('chatId', $this->chatId)->update(['mode' => 'done']);
				$this->triggerCommand(MainMenu::class);
			break;
			case 'setQSex':
			case 'setQAge':
			case 'setQDist':
			case 'setQProfile':
			case 'setQActivity':
				$this->db->table('questions')->where('id', $this->userData['questionId'])->delete();
				$this->db->table('users')->where('chatId', $this->chatId)->update(['mode' => 'done', 'questionId' => 0]);
				$this->triggerCommand(MainMenu::class);
			break;
			case 'setQText':
				$this->db->table('users')->where('chatId', $this->chatId)->update(['mode' => 'done']);
				$this->triggerCommand(MainMenu::class);
			break;
		}
	}

}