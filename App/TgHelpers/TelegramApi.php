<?php

namespace App\TgHelpers;

use PHPtricks\Orm\Database;

class TelegramApi {

	public static $result;
	public static $chatId;

	const TELEGRAM_API_KEY = '846036264:AAGGBYZ4xEJ2tvD0INF-M9XNzoxOZaPAol4';
	public static $API     = 'https://api.telegram.org/bot';

	public static function api($method, $params) {
		$url = self::$API.self::TELEGRAM_API_KEY.'/'.$method;

		return self::do($url, $params);
	}

	private static function do($url, $params) {
		$curl = curl_init();

		$params = json_encode($params);

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json', 'Content-Length: '.strlen($params),
		]);

		self::$result = json_decode(curl_exec($curl), TRUE);

		$db = Database::connect();
		$blocked = $db->table('users')->where('chatId', self::$chatId)->select(['userName', 'isBlocked'])->results();

		if (self::$result["ok"] === true) {
			if ($blocked['isBlocked']) {
				$db->table('users')->where('chatId', self::$chatId)->update(['isBlocked' => 0]);
			}
		}

		if (self::$result["ok"] === false) {
			if (self::$result["error_code"] == 403 && self::$result["description"] == "Forbidden: bot was blocked by the user") {
				$db->table('users')->where('chatId', self::$chatId)->update(['isBlocked' => 1]);
			}
		}

		return self::$result;

	}

	public static function isTyping() {
		self::api('sendChatAction', ['chat_id' => self::$chatId, 'action' => 'typing']);
	}

	public static function sendMessage($text, $chatId = null) {
		self::api('sendMessage', [
			'chat_id' => $chatId ? $chatId : self::$chatId, 'text' => $text, 'parse_mode' => 'HTML',
		]);
	}

	public static function sendMediaGroup($mediaGroup) {
		$mediaResult = [];
		foreach ($mediaGroup as $media) {
			$mediaResult[] = [
				'type' => 'photo',
				'media' => $media,
			];
		}

		self::api('sendMediaGroup', [
			'chat_id' => self::$chatId,
			'media' => json_encode($mediaResult)
		]);
	}

	public static function removeKeyboard($text) {
		self::api('sendMessage', [
			'chat_id'       => self::$chatId,
			'text' => $text,
			'reply_markup' => [
				'remove_keyboard' => true,
			],
			'parse_mode' => 'Markdown',
		]);
	}

	public static function sendMessageWithKeyboard($text, $encodedMarkup, $chatId = null) {
		self::api('sendMessage', [
			'chat_id' => $chatId ? $chatId : self::$chatId, 'text' => $text,
			'reply_markup' => [
				'keyboard' => $encodedMarkup,
				'one_time_keyboard' => false,
				'resize_keyboard'   => true,
			], 'parse_mode' => 'HTML',
		]);
	}

	public static function sendMessageWithInlineKeyboard($text, $buttons, $chatId = null) {
		self::api('sendMessage', [
			'chat_id' => $chatId ? $chatId : self::$chatId,
			'reply_markup' => [
				'inline_keyboard' => $buttons,
			],
			'text' => $text,
			'parse_mode' => 'HTML',
		]);
	}

	public static function removeMessageReplyMarkup($messageId, $callbackId) {
		self::api('editMessageReplyMarkup', [
			'chat_id' => self::$chatId,
			'inline_message_id' => $callbackId,
			'messageId' => $messageId,
			'reply_markup' => ['inline_keyboard' => []]
		]);
	}

	public static function editMessageText($callbackId, $text) {
		self::api('editMessageText', [
			'chat_id' => self::$chatId, 'inline_message_id' => $callbackId, 'text' => $text,
		]);
	}

	public static function answerCallbackQuery($callbackQueryId) {
		self::api('answerCallbackQuery', [
			'callback_query_id' => $callbackQueryId,
		]);
	}

	public static function showAlert($callbackQueryId, $text) {
		self::api('answerCallbackQuery', [
			'callback_query_id' => $callbackQueryId,
			'text' => $text,
			'showAlert' => true,
		]);
	}

	public static function deleteMessage($messageId) {
		self::api('deleteMessage', [
			'chat_id' => self::$chatId, 'message_id' => $messageId,
		]);
	}

	public static function updateMessageKeyboard($messageId, $newText, $newButton) {
		self::api('editMessageText', [
			'chat_id' => self::$chatId, 'message_id' => $messageId, 'text' => $newText, 'reply_markup' => [
				'inline_keyboard' => $newButton,
			],'parse_mode' => 'HTML',
		]);
	}

	public static function updateMessage($messageId, $newText) {
		self::api('editMessageText', [
			'chat_id' => self::$chatId, 'message_id' => $messageId, 'text' => $newText, 'parse_mode' => 'HTML',
		]);
	}

	public static function getChatMember($chatId, $userId) {
		return self::api('getChatMember', [
			'chat_id' => $chatId,
			'user_id' => $userId
		]);
	}

}