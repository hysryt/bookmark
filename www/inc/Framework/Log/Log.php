<?php

namespace Hysryt\Bookmark\Framework\Log;

use Exception;

/**
 * ロギング用のユーティリティクラス
 * 実際のロギング処理はPsr\Log\LoggerInterfaceを実装した他のクラスに任せる
 */
class Log {
	private static array $loggers = array();

	/**
	 * Psr\Log\LoggerInterfaceを実装したロガーを追加
	 *
	 * @param string $name
	 * @param \Psr\Log\LoggerInterface $logger
	 * @return void
	 */
	public static function addLogger(string $name, \Psr\Log\LoggerInterface $logger) {
		self::$loggers[$name] = $logger;
	}

	/**
	 * ロガーを削除
	 *
	 * @param string $name
	 * @return void
	 */
	public static function removeLogger(string $name) {
		if (isset(self::$loggers[$name])) {
			unset(self::$loggers[$name]);
		}
	}

	/**
	 * システムが動作しない（あまり使用しない）
	 */
	public static function emergency(string $message, array $context = array(), ?Exception $e = null) {
		self::log(LogLevel::EMERGENCY, $message, $context, $e);
	}

	/**
	 * すぐに対処が必要な状態
	 */
	public static function alert(string $message, array $context = array(), ?Exception $e = null) {
		self::log(LogLevel::ALERT, $message, $context, $e);
	}

	/**
	 * 危機的状態（あまり使用しない）
	 */
	public static function critical(string $message, array $context = array(), ?Exception $e = null) {
		self::log(LogLevel::CRITICAL, $message, $context, $e);
	}

	/**
	 * 直ちに修正する必要なはないが監視の必要があるランタイムエラー
	 */
	public static function error(string $message, array $context = array(), ?Exception $e = null) {
		self::log(LogLevel::ERROR, $message, $context, $e);
	}

	/**
	 * 非推奨のAPIが使用された、APIの悪い使い方、間違ってはないが望ましくないこと、など
	 */
	public static function warning(string $message, array $context = array(), ?Exception $e = null) {
		self::log(LogLevel::WARNING, $message, $context, $e);
	}

	/**
	 * 正常の動作だが、重要な出来事
	 */
	public static function notice(string $message, array $context = array(), ?Exception $e = null) {
		self::log(LogLevel::NOTICE, $message, $context, $e);
	}

	/**
	 * 正常の動作
	 */
	public static function info(string $message, array $context = array(), ?Exception $e = null) {
		self::log(LogLevel::INFO, $message, $context, $e);
	}

	/**
	 * デバッグ情報
	 */
	public static function debug(string $message, array $context = array(), ?Exception $e = null) {
		self::log(LogLevel::DEBUG, $message, $context, $e);
	}

	public static function log(int $level, $message, array $context = array(), ?Exception $e = null) {
		if ($e) {
			$context['exception'] = $e;
		}
		
		foreach (self::$loggers as $logger) {
			$logger->log($level, $message, $context);
		}
	}
}