<?php
$time_start = microtime(true);

define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT']);
require_once(SITE_ROOT.'/vendor/autoload.php');

class SendQuestion {

	public function __construct() {
		$db = \PHPtricks\Orm\Database::connect();
		$tg = new \App\TgHelpers\TelegramApi();

		$text = include(SITE_ROOT.'/App/config/lang/ua.php');
		$config = include(SITE_ROOT.'/App/config/config.php');


		$data = $db->query('
					SELECT 
					U.sex AS userSex, U.age AS userAge, U.chatId AS chatId,
					Q.question AS qQuestion, Q.id AS qId, Q.showProfile AS qShowProfile
					FROM questionQueue AS QQ
					INNER JOIN questionList AS Q ON QQ.questionId = Q.id
					INNER JOIN userList     AS U ON QQ.chatId = U.chatId
					WHERE status = \'new\'
					LIMIT 50')->results();

		if ($db->count()) {
			\App\TgHelpers\BuildQuestionMsg::$config = $config;
			\App\TgHelpers\BuildQuestionMsg::$text = $text;

			$idList = [];
			$n = 0;
			foreach ( $data as $item ) {
				$n++;
				$text = \App\TgHelpers\BuildQuestionMsg::buildMsg($item);
				$tgResponse = $tg->sendMessageWithInlineKeyboard($text[0], $text[1], 375036391);

				$idList[$n]['msgId']  = $tgResponse['result']['message_id'];
				$idList[$n]['qId']    = $item['qId'];
				$idList[$n]['chatId'] = $item['chatId'];
			}

			/*$db->query('UPDATE questionQueue
						SET tgId = '.$this->buildWhere($idList).' ');*/
			error_log($this->buildWhere($idList));
		}

	}

	private function buildWhere($idList) {
		$caseWhen     = ' (CASE ';
		$updateStatus = ', status = "sent" ';
		foreach ( $idList as $id ) {
			$caseWhen    .= ' WHEN questionId = '.$id['qId'].' AND chatId = '.$id['chatId'].' THEN '.$id['msgId'];
		}
		$caseWhen    .= ' end) ';

		return $caseWhen.$updateStatus;
	}

}

new SendQuestion();

error_log('SendQuestion '.(microtime(true) - $time_start));