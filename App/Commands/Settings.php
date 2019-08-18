<?php

namespace App\Commands;

use App\TgHelpers\TelegramApi;

class Settings extends BaseCommand {

	function processCommand($par = false) {
		TelegramApi::sendMessageWithKeyboard($this->text['settings'],
			[
				[$this->text['changeDistrict'], $this->text['getUpdates']],
				[$this->text['back']]
			]);
	}

}