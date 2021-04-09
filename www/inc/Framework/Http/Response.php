<?php

namespace Hysryt\Bookmark\Framework\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Response implements ResponseInterface {
	const STATUS_CODE_OK = 200;
	const STATUS_CODE_NOT_FOUND = 404;

	private string $protocolVersion = '1.1';
	private int $statusCode;
	private string $reasonPhrase;
	private array $headers;
	private StreamInterface $body;

	/**
	 * ステータスコード200（成功）のResponseを返す
	 *
	 * @param array $headers
	 * @param string $body
	 * @return ResponseInterface
	 */
	public static function ok($body = '', $headers = []): ResponseInterface {
		$bodyStream = new StringStream();
		$bodyStream->write($body);
		return new self(self::STATUS_CODE_OK, '', $headers, $bodyStream);
	}

	/**
	 * ステータスコード404（Not Found）のResponseを返す
	 *
	 * @param array $headers
	 * @param string $body
	 * @return ResponseInterface
	 */
	public static function notFound($body = '', $headers = []): ResponseInterface {
		$bodyStream = new StringStream();
		$bodyStream->write($body);
		return new self(self::STATUS_CODE_NOT_FOUND, '', $headers, $bodyStream);
	}

	/**
	 * @return body StreamInterface
	 */
	public function __construct(int $statusCode, $reasonPhrase = '', $headers = array(), $body = '') {
		$this->statusCode = $statusCode;
		$this->reasonPhrase = $reasonPhrase;
		$this->headers = $headers;
		$this->body = $body;
	}

	public function getStatusCode(): int {
		return $this->statusCode;
	}

	public function withStatus($code, $reasonPhrase = '') {
		$newResponse = clone $this;
		$this->statusCode = $code;
		$this->reasonPhrase = $reasonPhrase;
		return $newResponse;
	}

	public function getReasonPhrase() {
		return $this->reasonPhrase;
	}

	public function getHeaders() {
		return $this->headers;
	}

	public function hasHeader($name) {
		foreach ($this->headers as $headerName => $headerValue) {
			if (strtolower($name) === strtolower($headerName)) {
				return true;
			}
		}
		return false;
	}

	public function getHeaderLine($name) {
		if($this->hasHeader($name)) {
			return implode(',', $this->getHeader($name));
		}
		return '';
	}

	public function getHeader($name) {
		foreach($this->header as $headerName => $headerValue) {
			if (strtolower($name) === strtolower($headerName)) {
				return $headerValue;
			}
		}
		return array();
	}

	public function withHeader($name, $value) {
		if (!is_array($value)) {
			$value = [$value];
		}
		$newResponse = clone $this;
		$storedHeaderName = $this->getStoredHeaderName($name);
		$newResponse->headers[$storedHeaderName] = $value;
		return $newResponse;
	}

	public function withAddedHeader($name, $value) {
		if (!is_array($value)) {
			$value = [$value];
		}
		$newResponse = clone $this;
		$storedHeaderName = $this->getStoredHeaderName($name);
		$name = ($storedHeaderName) ? $storedHeaderName : $name;
		if (!isset($newResponse->header[$name])) {
			$$newResponse[$name] = [];
		}
		foreach($value as $item) {
			$newResponse->headers[$name][] = $item;
		}
		return $newResponse;
	}

	public function withoutHeader($name) {
		$newResponse = clone $this;
		unset($newResponse->headers[$name]);
		return $newResponse;
	}

	private function getStoredHeaderName($name) {
		foreach ($this->header as $headerName => $headerValue) {
			if (strtolower($headerName) === strtolower($name)) {
				return $headerName;
			}
		}
		return null;
	}

	public function getBody() {
		return $this->body;
	}

	public function withBody($body) {
		$newResponse = clone $this;
		$newResponse->body = $body;
		return $newResponse;
	}

	public function getProtocolVersion() {
		return $this->protocolVersion;
	}

	public function withProtocolVersion($version) {
		$newResponse = clone $this;
		$newResponse->protocolVersion = $version;
		return $newResponse;
	}

	public function isOk() {
		return $this->statusCode === self::STATUS_CODE_OK;
	}
}