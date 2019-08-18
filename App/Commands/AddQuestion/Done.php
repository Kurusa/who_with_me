<?php

namespace App\Commands\AddQuestion;

use App\Commands\BaseCommand;
use App\Commands\MainMenu;
use App\Commands\Moderate;
use App\TgHelpers\TelegramApi;

class Done extends BaseCommand {

	function processCommand($par = false) {
		$this->triggerCommand(MainMenu::class, $this->text['questionDone']);
		$this->triggerCommand(ModerateQuestion::class);

	}

}
