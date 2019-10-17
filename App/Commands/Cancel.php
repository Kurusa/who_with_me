<?php

namespace App\Commands;

class Cancel extends BaseCommand {

	function processCommand($par = false) {
		switch ($this->userData['mode']) {
			case 'setDistrict':
			case 'feedback':
				$this->db->table('userList')->where('chatId', $this->chatId)->update(['mode' => 'done']);
				$this->triggerCommand(MainMenu::class);
			break;
			case 'setQSex':
			case 'setQAge':
			case 'setQDist':
			case 'setQProfile':
			case 'setQActivity':
			case 'setQText':
				$this->db->query('DELETE FROM questionList WHERE id = '.$this->userData['questionId']);
				$this->db->table('userList')->where('chatId', $this->chatId)->update(['mode' => 'done', 'questionId' => 0]);
				$this->triggerCommand(MainMenu::class);
			break;
		}
	}

}