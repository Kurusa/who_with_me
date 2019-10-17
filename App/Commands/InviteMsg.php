<?php

namespace App\Commands;

class InviteMsg extends BaseCommand {

	function processCommand($par = false) {
		$limit = 3;

		if (!$par) {
			$this->db->table('inviteList')->where('inviterChatId', $this->chatId)->select()->results();
			if ($this->db->count()) {
				$limit += $this->db->count();
			}

			$link = 'https://telegram.me/newWhoBot?start='.$this->chatId;

			$this->tg->sendMessage($this->text['inviteMsgStart'].
				"\n"."\n".$link."\n"."\n".
				$this->text['inviteMsgEnd']."\n"."\n".
				$this->text['yourLimit'].$limit);
		} else {
			$invite = $this->db->table('inviteList')->where('chatId', $this->chatId)->select(['inviterChatId'])->results();
			if ($invite[0]['inviterChatId']) {
				$this->tg->sendMessage($this->text['newInviteUser'], $invite[0]['inviterChatId']);

				$this->db->table('inviteList')->where('inviterChatId', $invite[0]['inviterChatId'])->select()->results();
				if ($this->db->count()) {
					$limit += $this->db->count();
				}

				$this->tg->sendMessage($this->text['yourLimit'].$limit, $invite[0]['inviterChatId']);
			}
		}

	}

}