<?php

namespace App\TgHelpers;

class BuildQuestionMsg {
	
	static $config;
	static $text;
	
	//user      [sex, age]
	//question  [question, id, showProfile]
	static function buildMsg($data) {
		$flippedAge = array_flip(self::$config['ageMap']);
		$msg  = $data['qQuestion']."\n";
		$msg .= self::$text['questionFrom'].self::$config['sexMap'][$data['userSex']].', '.$flippedAge[$data['userAge']].self::$text['ages']."\n";

		$buttons = [
			[
				[
					'text' => self::$text['yes'], 'callback_data' => json_encode(['a' => 'yes', 'qId' => $data['qId'],]),
				], [
					'text' => self::$text['no'], 'callback_data' => json_encode(['a' => 'no', 'qId' => $data['qId']]),
				],
			],
		];

		if ($data['qShowProfile']) {
			$buttons[] = [
				[
					'text' => self::$text['showProfile'], 'callback_data' => json_encode(['a' => 'showProfile', 'qId' => $data['qId']]),
				],
			];
		}

		return [$msg, $buttons];
	}

}