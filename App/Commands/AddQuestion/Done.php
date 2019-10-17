<?php

namespace App\Commands\AddQuestion;

use App\Commands\BaseCommand;
use App\Commands\MainMenu;

class Done extends BaseCommand {

	function processCommand($par = false) {
		$this->db->table('questionList')->where('id', $this->userData['questionId'])->update(['done' => '1']);
		$this->triggerCommand(MainMenu::class, $this->text['questionDone']);
		$this->triggerCommand(ModerateQuestion::class);
	}

}
