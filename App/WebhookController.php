<?php

namespace App;

use App\Commands\AddQuestion\SetAge;
use App\Commands\AddQuestion\SetText;
use App\Commands\Feedback;
use App\Commands\InviteStart;
use App\Commands\Start;
use App\Commands\Unknown;
use \App\TgHelpers\TelegramApi;
use PHPtricks\Orm\Database;

class WebhookController {

	private $unknownCommand = false;

	public function run() {
		$update = \json_decode(file_get_contents('php://input'), TRUE, 512, JSON_UNESCAPED_UNICODE);
		$isCallback = !array_key_exists('message', $update);
		$response = $isCallback ? $update['callback_query'] : $update;
		$chatId = $response['message']['chat']['id'];

		if ($isCallback){
			$config = include('config/callback.php');

			$data = json_decode($response['data'], true, 512, JSON_UNESCAPED_UNICODE);
			$action = $data['a'];

			if (isset($config[$action])) {
				(new $config[$action]($response))->handle($response);
			} else {
				$this->unknownCommand = true;
			}

			$tg = new TelegramApi();
			$tg->answerCallbackQuery($response['id']);
		} else {
			$db = Database::connect();
			//$db->table('messageList')->insert(['chatId' => $chatId, 'text' => $update['message']['text'], 'date' => time()]);
			$user = $db->table('userList')->where('chatId', $chatId)->select(['mode'])->results();

			if ($update['message']['text']) {
				$text = $update['message']['text'];
				if (!$this->handleTextCommand($text, $update)) {
					$this->handleModeCommand($user[0], $update);
				}
			} else {
				$this->unknownCommand = true;
			}
		}

		if ($this->unknownCommand) {
			(new Unknown())->handle($update);
		}
	}

	public function handleTextCommand($text, $update) {
		if (strpos($text, '/') === 0) {
			if (strpos($text, '/start') !== false) {
				$textStrings = explode(' ', $text);
				if (isset($textStrings[1])) {
					$token = $textStrings[1];
					(new Start())->handle($update, $token);
					exit;
				}
			}
			$handlers = include('config/commands.php');
		} else {
			$handlers = include('config/keyboardCommands.php');
		}

		if (isset($handlers[$text])) {
			(new $handlers[$text]($update))->handle($update);
			return true;
		} else {
			return false;
		}
	}

	public function handleModeCommand($user, $update) {
		$handlers = include('config/modesCommands.php');

		if ($handlers[$user['mode']]) {
			(new $handlers[$user['mode']]($update))->handle($update);
		} else {
			$this->unknownCommand = true;
		}
	}

}

