<?php

namespace App\TgHelpers;

class TelegramParser {

	private static $data;

	public function __construct($data) {
		self::$data = $data;
	}

	public static function isGroup() {
		return (self::$data['message']['chat']['type'] == 'group') ? true : false;
	}

	public static function isCallback() {
		return array_key_exists('data', self::$data);
	}

	public static function getMsgId() {
		return self::$data['message']['message_id'];
	}

	public static function getUserName() {
		return strval(self::$data['message']['chat']['username']);
	}

	public static function getGroupTitle() {
		return strval(self::$data['message']['chat']['title']);
	}

	public static function getMessage() {
		return self::$data['reply_to_message']['text'] ? self::$data['reply_to_message']['text'] : self::$data['message']['text'];
	}

	public static function getChatId() {
		return intval(self::$data['message']['chat']['id']);
	}

	public static function getWholeCallback() {
		return self::$data['data'];
	}

	public static function getCallbackByKey($key) {
		return json_decode(self::getWholeCallback(), true)[strval($key)];
	}

	public static function getIdFromCallback() {
		return json_decode(self::getWholeCallback(), true)['id'];
	}

	public static function getChatIdFromCallback() {
		return self::$data['message']['chat']['id'];
	}

	public static function getCallbackId() {
		return self::$data['id'];
	}
}