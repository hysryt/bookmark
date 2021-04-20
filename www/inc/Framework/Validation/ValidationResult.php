<?php

namespace Hysryt\Bookmark\Framework\Validation;

/**
 * バリデーション結果
 */
class ValidationResult {
	/**
	 * バリデーションエラーを格納するマップ
	 * キーに対して複数のエラーが入る可能性があるため値は配列
	 *
	 * @var array<string,array<string>>
	 */
	private $errors = [];

	/**
	 * 指定したキーにエラーを追加
	 *
	 * @param string $name
	 * @param string $value
	 * @return void
	 */
	public function addError($name, $value) {
		if (!$this->hasError($name)) {
			$this->errors[$name] = [];
		}
		$this->errors[$name][] = $value;
	}

	/**
	 * 全てのエラーを取得
	 * 
	 * @return array
	 */
	public function getAllErrors() {
		return $this->errors;
	}

	/**
	 * 指定したキーに紐づくエラーを取得
	 *
	 * @param string $name
	 * @return array<string>
	 */
	public function getErrors($name) {
		if ($this->hasError($name)) {
			return $this->errors[$name];
		}
		return array();
	}

	/**
	 * 指定したキーにエラーがあるかどうか
	 *
	 * @param string $name
	 * @return bool
	 */
	public function hasError($name): bool {
		return isset($this->errors[$name]);
	}

	/**
	 * バリデーション結果に異常がない場合にtrueを返す
	 *
	 * @return boolean
	 */
	public function isOk(): bool {
		return count($this->errors) === 0;
	}

	/**
	 * バリデーション結果に異常がある場合にtrueを返す
	 *
	 * @return boolean
	 */
	public function isError(): bool {
		return !$this->isOk();
	}
}