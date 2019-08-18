<?php
define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT']);
require_once(SITE_ROOT.'/vendor/autoload.php');

class AddToQueue {

	/**
	 * @var \PHPtricks\Orm\Database
	 */
	private $db;

	public function __construct() {
		$this->db = \PHPtricks\Orm\Database::connect();
		$questionList = $this->db->table('questions')->
		where('moderated', '1')->
		where('inQueue', '0')->
		where('active', '1')->
		limit(5)->
		select()->results();

		foreach ($questionList as $question) {
			$this->db->query('INSERT INTO questionQueue(questionId, chatId)
							  SELECT '.$question['id'].' as questionId, chatId FROM users '.$this->buildWhere($question));

			$this->db->table('questions')->where('id', $question['id'])->update(['inQueue' => '1']);
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

		return $where;
	}

}

new AddToQueue();