<?php

namespace App;

use App\Commands\AddQuestion\SetAge;
use App\Commands\AddQuestion\SetText;
use App\Commands\Feedback;
use App\Commands\Unknown;
use \App\TgHelpers\TelegramApi;
use PHPtricks\Orm\Database;

class WebhookController {

	public function run() {
		$update = \json_decode(file_get_contents('php://input'), TRUE);
		$isCallback = !array_key_exists('message', $update);
		$response = $isCallback ? $update['callback_query'] : $update;
		$chatId = $response['message']['chat']['id'];

		if ($isCallback) {
			$config = include('config/callback.php');

			$data = \json_decode($response['data'], true);
			$action = $data['a'];

			if (isset($config[$action])) {
				(new $config[$action]($response))->handle($response);
			} else {
				$unknownCommand = true;
			}

			TelegramApi::answerCallbackQuery($response['id']);
		} else {
			$db = Database::connect();
			$db->table('messageList')->insert(['chatId' => $chatId, 'text' => $update['message']['text'], 'date' => time()]);
			$user = $db->table('users')->where('chatId', $chatId)->select(['mode'])->results();

			if ($update['message']['text']) {
				$text = $update['message']['text'];
				if (strpos($text, '/') === 0) {
					$handlers = include('config/commands.php');
				} else {
					$handlers = include('config/keyboardCommands.php');
				}

				if (isset($handlers[$text])) {
					if ($user[0]['mode'] == 'setQAge' && $text !== '✖️ скасувати') {
						(new SetAge())->handle($update);
						exit;
					}
					(new $handlers[$text]($update))->handle($update);
				} else {
					if ($user[0]['mode'] == 'feedback') {
						(new Feedback())->handle($update);
					} elseif ($user[0]['mode'] == 'setQText') {
						(new SetText())->handle($update);
					} else {
						$unknownCommand = true;
					}
				}
			} else {
				$unknownCommand = true;
			}
		}

		if ($unknownCommand) {
			(new Unknown())->handle($update);
		}
	}

}

