<?php

use App\TgHelpers\TelegramApi;

define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT']);
require_once(SITE_ROOT.'/vendor/autoload.php');

class SendFromQueue {

	/**
	 * @var \PHPtricks\Orm\Database
	 */
	private $db;

	public function __construct() {
		$this->db = \PHPtricks\Orm\Database::connect();
		$queueList = $this->db->table('questionQueue')->
		where('status', 'new')->
		limit(5)->select()->results();

		foreach ($queueList as $item) {
			$questionData   = $this->db->table('questions')->where('id', $item['questionId'])->select()->results();
			$userData       = $this->db->table('users')->where('chatId', $item['chatId'])->select()->results();

			$text = $this->buildMsg($questionData, $userData);
			TelegramApi::sendMessageWithInlineKeyboard($text[0], $text[1], $item['chatId']);

			$this->db->table('questionQueue')->
			where('chatId', $item['chatId'])->
			where('questionId', $item['questionId'])->
			update(['status' => 'sent']);
		}
	}


	private function buildMsg($questionData, $user) {
		$config   = include(SITE_ROOT.'/App/config/config.php');
		$text     = include(SITE_ROOT.'/App/config/lang/ua.php');

		$flippedAge = array_flip($config['ageMap']);
		$msg  = $questionData[0]['question']."\n";
		$msg .= $text['questionFrom'].$config['sexMap'][$user[0]['sex']].', '.$flippedAge[$user[0]['age']].$text['ages']."\n";

		$buttons = [
			[
				[
					'text' => $text['yes'], 'callback_data' => json_encode(['a' => 'yes', 'qId' => $questionData[0]['id'],]),
				], [
					'text' => $text['no'], 'callback_data' => json_encode(['a' => 'no', 'qId' => $questionData[0]['id'], 'sId' => $user[0]['chatId']]),
				],
			],
		];

		if ($questionData[0]['showProfile']) {
			$buttons[] = [
				[
					'text' => $text['showProfile'], 'callback_data' => json_encode(['a' => 'showProfile']),
				],
			];
		}

		return [$msg, $buttons];
	}


}

new SendFromQueue();