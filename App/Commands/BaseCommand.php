<?php

namespace App\Commands;

define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT']);

use App\TgHelpers\TelegramParser;
use App\TgHelpers\TelegramApi;
use PHPtricks\Orm\Database;

abstract class BaseCommand {

	/**
	 * @var TelegramParser
	 */
	protected $tgParser;

	/**
	 * @var Database
	 */
	protected $db;
	protected $chatId;
	protected $userData;

	protected $text;
	protected $config;

	private $update;

	function handle(array $update, $par = false) {
		$this->update    = $update;
		$this->db        = Database::connect();
		$this->tgParser  = new TelegramParser($this->update);
		$this->chatId    = $this->tgParser::getChatId();

		TelegramApi::$chatId = $this->chatId;

		$data = $this->db->table('users')->where('chatId', $this->chatId)->select()->results();
		$this->userData = $data[0];

		$this->text     = include(SITE_ROOT.'/App/config/lang/ua.php');
		$this->config   = include(SITE_ROOT.'/App/config/config.php');

		$this->processCommand($par);
	}

	function triggerCommand($class, $par = false) {
		(new $class())->handle($this->update, $par);
	}

	abstract function processCommand($par = false);

}