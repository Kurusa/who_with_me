<?php

namespace App\Commands;

class Unknown extends BaseCommand {

	function processCommand($par = false) {
		if ($this->userData['mode'] == 'done') {
			$this->triggerCommand(MainMenu::class, $this->text['unknownCommand']);
		} else {
			$this->tg->sendMessage($this->text['unknownCommand']);
		}
	}

}