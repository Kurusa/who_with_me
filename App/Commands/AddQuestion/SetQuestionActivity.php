<?php

namespace App\Commands\AddQuestion;

use App\Commands\BaseCommand;

class SetQuestionActivity extends BaseCommand {

	private $mode = 'setQActivity';

	function processCommand($par = false) {
		if ($this->userData['mode'] == $this->mode) {
			if ($this->tgParser::getCallbackByKey('a') !== 'changeDone') {
				$this->tg->updateMessageKeyboard($this->tgParser::getMsgId(), $this->text['howMuchActive'], $this->buildButtons($this->tgParser::getCallbackByKey('num')));
			} else {
				$hours = $this->tgParser::getCallbackByKey('num');
				if ($hours !== 0) {
					$timeInFuture = time() + (60 * 60 * $hours);
					$this->db->table('questionList')->where('id', $this->userData['questionId'])->update(['upTo' => $timeInFuture]);
					$this->tg->deleteMessage($this->tgParser::getMsgId());
					$this->triggerCommand(Done::class);
				} else {
					$this->tg->showAlert($this->tgParser::getCallbackId(), $this->text['moreThanHour']);
				}
			}
		} else {
			$this->db->table('userList')->where('chatId', $this->chatId)->update(['mode' => $this->mode]);
			$this->tg->sendMessageWithKeyboard($this->text['useButtons'], [[$this->text['cancel']]]);
			$this->tg->sendMessageWithInlineKeyboard($this->text['howMuchActive'], $this->buildButtons(0));
		}

	}

	private function buildButtons($hours) {
		$hours       = ($hours < 0) ? 0 : $hours;
		$hours       = ($hours >= 168) ? 168 : $hours;
		$num         = $hours/24;
		$daysCount   = (floor($num) < 0) ? 0 : floor($num);
		$hoursCount  = ($hours%24 < 0)   ? 0 : ($hours%24);

		$buttons = [
			[
				[
					'text' => $this->text['minus'], 'callback_data' => json_encode(['a' => 'change', 'num' => $hours - 1]),
				],
				['text' => $hoursCount.$this->text['hours'], 'callback_data' => json_encode([])],
				[
					'text' => $this->text['plus'], 'callback_data' => json_encode(['a' => 'change', 'num' => $hours + 1]),
				],
			],
			[
				[
					'text' => $this->text['minus'], 'callback_data' => json_encode(['a' => 'change', 'num' => $hours - 24]),
				],
				['text' => $daysCount.$this->text['days'], 'callback_data' => json_encode([])],
				[
					'text' => $this->text['plus'], 'callback_data' => json_encode(['a' => 'change', 'num' => $hours + 24]),
				],
			],
			[
				[
					'text' => $this->text['done'], 'callback_data' => json_encode(['a' => 'changeDone', 'num' => $hours]),
				],
			],
		];

		return $buttons;
	}

}
