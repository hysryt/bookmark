<?php

namespace Hysryt\Bookmark\Framework\Log;

use Hysryt\Bookmark\Framework\Log\LogLevel;
use DateTimeZone;
use DateTimeImmutable;

/**
 * ファイルへのロギング用クラス
 */
class FileLogger implements \Psr\Log\LoggerInterface {
	const LEVELS = [
		LogLevel::EMERGENCY => 'EMERGENCY',
		LogLevel::ALERT     => 'ALERT',
		LogLevel::CRITICAL  => 'CRITICAL',
		LogLevel::ERROR     => 'ERROR',
		LogLevel::WARNING   => 'WARNING',
		LogLevel::NOTICE    => 'NOTICE',
		LogLevel::INFO      => 'INFO',
		LogLevel::DEBUG     => 'DEBUG',
	];

	private string $logFilepath;
	private bool $isProduction;
	private DateTimeZone|null $timezone;

	public function __construct(string $logFilepath, bool $isProduction = false, DateTimeZone|null $timezone = null) {
		$this->logFilepath = $logFilepath;
		$this->isProduction = $isProduction;
		$this->timezone = $timezone;
	}

	/**
	 * システムが使用できない状態
	 *
	 * @param string  $message
	 * @param array $context
	 * @return void
	 */
	public function emergency($message, array $context = array()) {
		$this->log(LogLevel::EMERGENCY, $message, $context);
	}

	/**
	 * 対処の必要がある状態
	 * 例：サイト全体のダウン、DBに接続できないなど
	 * 担当者をSMSで呼び出す必要があるような状態
	 *
	 * @param string $message
	 * @param array $context
	 * @return void
	 */
	public function alert($message, array $context = array()) {
		$this->log(LogLevel::ALERT, $message, $context);
	}

	/**
	 * 危機的状態
	 * 例：アプリケーションのコンポーネントが動作しない、想定外の例外が発生した
	 *
	 * @param string $message
	 * @param array $context
	 * @return void
	 */
	public function critical($message, array $context = array()) {
		$this->log(LogLevel::CRITICAL, $message, $context);
	}

	/**
	 * 直ちに修正する必要なはないが監視の必要があるランタイムエラー
	 *
	 * @param string $message
	 * @param array $context
	 * @return void
	 */
	public function error($message, array $context = array()) {
		$this->log(LogLevel::ERROR, $message, $context);
	}

	/**
	 * error以外の想定外の出来事
	 * 例：非推奨のAPIが使用された、APIの悪い使い方、間違ってはないが望ましくないこと
	 *
	 * @param [type] $message
	 * @param array $context
	 * @return void
	 */
	public function warning($message, array $context = array()) {
		$this->log(LogLevel::WARNING, $message, $context);
	}

	/**
	 * 正常の動作だが、重要な出来事
	 *
	 * @param [type] $message
	 * @param array $context
	 * @return void
	 */
	public function notice($message, array $context = array()) {
		$this->log(LogLevel::NOTICE, $message, $context);
	}

	/**
	 * 何かしらのイベント
	 *
	 * @param string $message
	 * @param array $context
	 * @return void
	 */
	public function info($message, array $context = array()) {
		$this->log(LogLevel::INFO, $message, $context);
	}

	/**
	 * デバッグ情報
	 *
	 * @param string $message
	 * @param array $context
	 * @return void
	 */
	public function debug($message, array $context = array()) {
		$this->log(LogLevel::DEBUG, $message, $context);
	}

	/**
	 * ログ出力
	 *
	 * @param int $level
	 * @param string $message
	 * @param array $context
	 * @return void
	 */
	public function log($level, $message, array $context = array()) {
		// 本番環境ではdebugレベルのログは取得しない
		if ($this->isProduction && $level === LogLevel::DEBUG) {
			return;
		}

		$levelName = self::LEVELS[$level];
		$time = (new DateTimeImmutable("now", $this->timezone))->format('c');
		$pid = getmypid();
		$log = "{$time} {$pid} [{$levelName}] {$message}\n";
		file_put_contents($this->logFilepath, $log, FILE_APPEND | LOCK_EX);
	}
}