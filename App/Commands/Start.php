<?php

namespace App\Commands;

use App\WebhookController;

class Start extends BaseCommand {

	function processCommand($par = false) {
		if (!empty($this->userData)) {
			if ($this->userData['mode'] == 'done') {
				$this->triggerCommand(MainMenu::class);
			} else {
				(new WebhookController())->handleModeCommand($this->userData, $this->update);
			}
		} else {
			$userName = $this->tgParser::isGroup() ? $this->tgParser::getGroupTitle() : $this->tgParser::getUserName();
			$this->db->table('userList')->insert(['chatId' => $this->tgParser::getChatId(), 'userName' => $userName, 'date' => time()]);
			$this->tg->sendMessage($this->text['hello']);
			$this->triggerCommand(Sex::class);

			if ($par) {
				$this->db->table('inviteList')->insert(['chatId' => $this->tgParser::getChatId(), 'inviterChatId' => $par, 'date' => time()]);
			}
		}
	}

}

