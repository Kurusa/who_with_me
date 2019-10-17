<?php

namespace App\Commands;

class Settings extends BaseCommand {

	function processCommand($par = false) {
		$this->tg->sendMessageWithKeyboard($this->text['settings'],
			[
				[$this->text['changeDistrict'], $this->text['getUpdates']],
				[$this->text['back']]
			]);
	}

}