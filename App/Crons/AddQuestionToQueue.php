<?php
$time_start = microtime(true);

define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT']);
require_once(SITE_ROOT.'/vendor/autoload.php');

class AddQuestionToQueue {

	public function __construct() {
		$db = \PHPtricks\Orm\Database::connect();
		$questionList = $db->table('questionList')->
		where('moderated', '1')->
		where('inQueue', '0')->
		where('active', '1')->
		where('done', '1')->
		limit(50)->
		select()->results();

		if ($db->count()) {
			$qIdList = [];
			foreach ( $questionList as $question ) {
				$db->query('INSERT INTO questionQueue(questionId, chatId)
							  SELECT '.$question['id'].' as questionId, chatId FROM userList '.$this->buildWhere($question));

				$qIdList[] = $question['id'];
			}

			$db->query('UPDATE questionList SET inQueue = 1 WHERE id IN('.implode($qIdList, ',').')');
		}
	}

	private function buildWhere($question) {
		$where = '';

		if ($question['sex'] !== 'all') {
			$where .= ' WHERE sex = '. $question['sex'];
		}

		if ($question['age'] !== 'all') {
			$where .= $where ? ' AND ' : ' WHERE ';
			$where .= ' age = '. $question['age'];
		}

		if ($question['district'] !== 'all') {
			$where .= $where ? ' AND ' : ' WHERE ';
			$where .= ' district = '. $question['district'];
		}

		$where .= $where ? ' AND ' : ' WHERE ';
		$where .= ' chatId <> '. $question['chatId'];
		$where .= ' AND newUpdate = 1 ';
		$where .= ' AND chatId > 0 ';

		return $where;
	}

}

new AddQuestionToQueue();

error_log('AddQuestionToQueue '.(microtime(true) - $time_start));