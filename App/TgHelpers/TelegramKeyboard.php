<?php

namespace App\TgHelpers;

class TelegramKeyboard {

	static $columns;
	static $list;

	static $buttonText;

	static $action;
	static $id;

	static $buttons = [];

	static function build() {
		$one_row = [];

		foreach ( self::$list as $key => $listKey ) {
			$one_row[] = [
				'text' => $listKey[self::$buttonText], 'callback_data' => json_encode([
					'a' => self::$action, 'id' => $listKey[self::$id],
				]),
			];

			if (count($one_row) == self::$columns) {
				self::$buttons[] = $one_row;
				$one_row = [];
			}
		}

		if (count($one_row) > 0) {
			self::$buttons[] = $one_row;
		}
	}

	static function addButton($text, $callback) {
		self::$buttons[] = [
			[
				'text' => $text, 'callback_data' => json_encode($callback),
			],
		];
	}

	static function get() {
		return self::$buttons;
	}

}