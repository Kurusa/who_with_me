<?php

define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT']);
require_once(SITE_ROOT.'/vendor/autoload.php');

use App\TgHelpers\TelegramApi;
use PHPtricks\Orm\Database;

class SendQuestion {

	function __construct() {
		$db           = Database::connect();
		$questionData = $db->table('questions')->
		where('moderated', '1')->
		where('active', '1')->
		limit(2)->
		select()->results();

		foreach ($questionData as $questionDatum) {
			//$db->table('questions')->where('id', $questionDatum['id'])->update(['sent' => '1']);

			$users = $db->table('users');
			if ($questionDatum['sex'] !== '2') {
				$users->where('sex', $questionDatum['sex']);
			}

			if ($questionDatum['age'] !== 'all') {
				$users->where('age', $questionDatum['age']);
			}

			if ($questionDatum['district'] !== 'all') {
				$users->where('district', $questionDatum['district']);
			}

			$users->where('chatId', '<>', $questionDatum['chatId']);
			$users->where('newUpdate', '1');
			$users->addToQuery(" AND mode NOT IN ('sex', 'age', 'district', 'feedback')");
			$users->addToQuery(' AND chatId NOT IN (SELECT chatId FROM seen WHERE questionId ='.$questionDatum['id'].')');
			$list = $users->select(['chatId'])->results();

			$senderData = $db->table('users')->where('chatId', $questionDatum['chatId'])->select()->results();

			if ($list) {
				foreach ( $list as $item ) {
					$msg = $this->buildMsg($questionDatum, $senderData);
					$db->table('users')->where('chatId', $item['chatId'])->update(['mode' => 'readQuestion', 'questionId' => $questionDatum['questionId']]);
					TelegramApi::sendMessageWithInlineKeyboard($msg[0], $msg[1], $item['chatId']);
				}
			}
		}
	}

	private function buildMsg($questionData, $user) {
		$config   = include(SITE_ROOT.'/App/config/config.php');
		$text     = include(SITE_ROOT.'/App/config/lang/ua.php');

		$flippedAge = array_flip($config['ageMap']);
		$msg   = $questionData['question']."\n";
		$msg  .= $text['questionFrom'].$config['sexMap'][$user[0]['sex']].', '.$flippedAge[$user[0]['age']].$text['ages']."\n";

		$buttons = [
			[
				[
					'text' => $text['yes'], 'callback_data' => json_encode(['a' => 'yes', 'qId' => $questionData['id']]),
				], [
					'text' => $text['no'], 'callback_data' => json_encode(['a' => 'no', 'qId' => $questionData['id']]),
				],
			],
		];

		if ($questionData['showProfile']) {
			$buttons[] = [
				[
					'text' => $text['showProfile'], 'callback_data' => json_encode(['a' => 'showProfile']),
				],
			];
		}

		return [$msg, $buttons];
	}

}

new SendQuestion();