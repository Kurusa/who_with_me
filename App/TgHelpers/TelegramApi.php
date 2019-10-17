<?php

namespace App\TgHelpers;

class TelegramApi {

	public $result;
	public $chatId;
	public $curl;

	const TELEGRAM_API_KEY = '';
	public $API = 'https://api.telegram.org/bot';

	public function __construct() {
		$this->curl = curl_init();
	}

	public function api($method, $params) {
		$url = $this->API.self::TELEGRAM_API_KEY.'/'.$method;

		return self::do($url, $params);
	}

	private function do($url, $params) {

		$params = json_encode($params);

		curl_setopt($this->curl , CURLOPT_URL, $url);
		curl_setopt($this->curl , CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($this->curl , CURLOPT_POSTFIELDS, $params);
		curl_setopt($this->curl , CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl , CURLOPT_HTTPHEADER, [
			'Content-Type: application/json', 'Content-Length: '.strlen($params),
		]);

		$this->result = json_decode(curl_exec($this->curl), TRUE);

		return $this->result;
	}

	public function isTyping() {
		self::api('sendChatAction', ['chat_id' => $this->chatId, 'action' => 'typing']);
	}

	public function sendMessage($text, $chatId = null) {
		self::api('sendMessage', [
			'chat_id' => $chatId ? $chatId : $this->chatId, 'text' => $text, 'parse_mode' => 'HTML',
		]);
	}

	public function sendMediaGroup($mediaGroup) {
		$mediaResult = [];
		foreach ($mediaGroup as $media) {
			$mediaResult[] = [
				'type' => 'photo',
				'media' => $media,
			];
		}

		self::api('sendMediaGroup', [
			'chat_id' => $this->chatId,
			'media' => json_encode($mediaResult)
		]);
	}

	public function removeKeyboard($text) {
		self::api('sendMessage', [
			'chat_id'       => $this->chatId,
			'text' => $text,
			'reply_markup' => [
				'remove_keyboard' => true,
			],
			'parse_mode' => 'Markdown',
		]);
	}

	public function sendMessageWithKeyboard($text, $encodedMarkup, $chatId = null) {
		self::api('sendMessage', [
			'chat_id' => $chatId ? $chatId : $this->chatId, 'text' => $text,
			'reply_markup' => [
				'keyboard' => $encodedMarkup,
				'one_time_keyboard' => false,
				'resize_keyboard'   => true,
			], 'parse_mode' => 'HTML',
		]);
	}

	public function sendMessageWithInlineKeyboard($text, $buttons, $chatId = null) {
		return self::api('sendMessage', [
			'chat_id' => $chatId ? $chatId : $this->chatId,
			'reply_markup' => [
				'inline_keyboard' => $buttons,
			],
			'text' => $text,
			'parse_mode' => 'HTML',
		]);
	}

	public function removeMessageReplyMarkup($messageId, $callbackId) {
		self::api('editMessageReplyMarkup', [
			'chat_id' => $this->chatId,
			'inline_message_id' => $callbackId,
			'messageId' => $messageId,
			'reply_markup' => ['inline_keyboard' => []]
		]);
	}

	public function editMessageText($callbackId, $text) {
		self::api('editMessageText', [
			'chat_id' => $this->chatId, 'inline_message_id' => $callbackId, 'text' => $text,
		]);
	}

	public function answerCallbackQuery($callbackQueryId) {
		self::api('answerCallbackQuery', [
			'callback_query_id' => $callbackQueryId,
		]);
	}

	public function showAlert($callbackQueryId, $text) {
		self::api('answerCallbackQuery', [
			'callback_query_id' => $callbackQueryId,
			'text' => $text,
			'showAlert' => true,
		]);
	}

	public function deleteMessage($messageId) {
		self::api('deleteMessage', [
			'chat_id' => $this->chatId, 'message_id' => $messageId,
		]);
	}

	public function updateMessageKeyboard($messageId, $newText, $newButton) {
		self::api('editMessageText', [
			'chat_id' => $this->chatId, 'message_id' => $messageId, 'text' => $newText, 'reply_markup' => [
				'inline_keyboard' => $newButton,
			],'parse_mode' => 'HTML',
		]);
	}

	public function updateMessage($messageId, $newText) {
		self::api('editMessageText', [
			'chat_id' => $this->chatId, 'message_id' => $messageId, 'text' => $newText, 'parse_mode' => 'HTML',
		]);
	}

	public function getChatMember($chatId, $userId) {
		return self::api('getChatMember', [
			'chat_id' => $chatId,
			'user_id' => $userId
		]);
	}

	public function __destruct() {
		$this->curl = curl_close($this->curl);
	}

}