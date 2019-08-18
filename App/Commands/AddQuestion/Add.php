<?php

namespace App\Commands\AddQuestion;

use App\Commands\BaseCommand;

class Add extends BaseCommand {

	function processCommand($par = false) {
		$this->db->table('questions')->insert(['chatId' => $this->chatId, 'question' => '', 'date' => time()]);
		$this->db->table('users')->where('chatId', $this->chatId)->update(['questionId' => $this->db->lastInsertedId()]);

		$this->triggerCommand(SetText::class);
	}

}