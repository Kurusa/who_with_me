<?php

namespace App\Commands;

class Unknown extends BaseCommand {

	function processCommand($par = false) {
		$this->triggerCommand(MainMenu::class, $this->text['unknownCommand']);
	}

}