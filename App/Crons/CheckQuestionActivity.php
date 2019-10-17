<?php

define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT']);
require_once(SITE_ROOT.'/vendor/autoload.php');

class CheckQuestionActivity {

	public function __construct() {
		$db = \PHPtricks\Orm\Database::connect();

		$db->table('questionList AS Q')->
		where('active', '1')->
		where('done', '1')->
		where('upTo', '<=', time())->
		update(['active' => '0']);
	}

}

new CheckQuestionActivity();