<?php

define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT']);
require_once(SITE_ROOT.'/vendor/autoload.php');

class CheckQuestionActivity {

	public function __construct() {
		$db = \PHPtricks\Orm\Database::connect();
		$text = include(SITE_ROOT.'/App/config/lang/ua.php');

		$questionsList = $db->table('questions AS Q')->
		where('active', '1')->
		where('upTo', '<=', time())->
		select(['id', 'upTo', 'question', 'chatId', '(SELECT noActiveUpdate FROM users WHERE chatId = Q.chatId) AS updateStatus'])->results();

		foreach ( $questionsList as $question ) {
			$db->table('questions')->where('id', $question['id'])->update(['active' => '0']);
			if ($question['updateStatus'] == 1) {
				\App\TgHelpers\TelegramApi::sendMessage($text['yourQuestion'].$question['question'].$text['notActive'], $question['chatId']);
			}
		}
	}

}

new CheckQuestionActivity();