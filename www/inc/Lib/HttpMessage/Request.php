<?php

namespace Hysryt\Bookmark\Lib\HttpMessage;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ServerRequestInterface;

class Request implements ServerRequestInterface {
	private array $serverParams;
	private array $cookieParams;
	private array $queryParams;
	private array $parsedBody;
	private array $uploadedFiles;
	private array $attributes;
	private UriInterface $uri;
	
	public function __construct($serverParams, $cookieParams, $queryParams, $parsedBody, $uploadedFiles, $headers, $attributes = array()) {
		$this->serverParams = $serverParams;
		$this->cookieParams = $cookieParams;
		$this->queryParams = $queryParams;
		$this->parsedBody = $parsedBody;
		$this->uploadedFiles = $uploadedFiles;
		$this->headers = $headers;
		$this->attributes = $attributes;
		$this->uri = $this->createUri();
	}

	/**
	 * Requestインスタンスを生成
	 * 
	 * @param string $method
	 * @param string $url
	 * @return Request
	 */
	public static function create(string $method, string $url) {
		$request = new Request([
			'REQUEST_METHOD' => $method,
		],[],[],[],[],[]);
		$request = $request->withUri(Uri::createFromUriString($url));
		return $request;
	}

	public function getServerParams() {
		return $this->serverParams;
	}

	public function getCookieParams() {
		return $this->cookieParams;
	}

	public function getQueryParams() {
		return $this->queryParams;
	}

	public function getUploadedFiles() {
		return $this->uploadedFiles;
	}

	public function getParsedBody() {
		return $this->parsedBody;
	}

	public function getRequestTarget() {
		return isset($this->serverParams['SCRIPT_URL']) ? $this->serverParams['SCRIPT_URL'] : '/';
	}

	public function getMethod() {
		return isset($this->serverParams['REQUEST_METHOD']) ? $this->serverParams['REQUEST_METHOD'] : '';
	}

	public function hasHeader($name) {
		foreach (array_keys($this->headers) as $requestHeaderName) {
			if (strtolower($requestHeaderName) === strtolower($name)) {
				return true;
			}
		}
		return false;
	}

	public function getHeaderLine($name) {
		foreach (array_keys($this->headers) as $requestHeaderName => $value) {
			if (strtolower($requestHeaderName) === strtolower($name)) {
				return $value;
			}
		}
		return '';
	}

	public function getAttributes(){
		return $this->attributes;
	}

	public function getAttribute($name, $default = null){
		return isset($this->attributes[$name]) ? $this->attributes[$name] : $default;
	}

	public function withAttribute($name, $value){
		$newRequest = clone $this;
		$newRequest->attributes[$name] = $value;
		return $newRequest;
	}

	public function getUri(){
		return $this->uri;
	}

	public function withUri(UriInterface $uri, $preserveHost = false){
		$newRequest = clone $this;
		$newRequest->uri = $uri;
		return $newRequest;
	}

	/** @deprecated */
	public function withCookieParams(array $cookieParams){}

	/** @deprecated */
	public function withQueryParams(array $queryParams){}

	/** @deprecated */
	public function withUploadedFiles(array $uploadedFiles){}

	/** @deprecated */
	public function withParsedBody($parsedBody){}

	/** @deprecated */
	public function withoutAttribute($name){}

	/** @deprecated */
	public function withRequestTarget($requestTarget){}

	/** @deprecated */
	public function withMethod($method){}

	/** @deprecated */
	public function getProtocolVersion(){}

	/** @deprecated */
	public function withProtocolVersion($version){}

	/** @deprecated */
	public function getHeader($name) {}

	/** @deprecated */
	public function getHeaders() {}

	/** @deprecated */
	public function withHeader($name, $value) {}

	/** @deprecated */
	public function withAddedHeader($name, $value) {}

	/** @deprecated */
	public function withoutHeader($name) {}

	/** @deprecated */
	public function getBody() {}

	/** @deprecated */
	public function withBody($body) {}

	private function createUri() {
		$scheme = 'http';
		if (isset($this->serverParams['HTTPS']) && $this->serverParams['HTTPS']) {
			$scheme = 'https';
		}

		$host = '';
		if (isset($this->serverParams['HTTP_HOST'])) {
			$host = $this->serverParams['HTTP_HOST'];
		}

		$path = '';
		if (isset($this->serverParams['REQUEST_URI'])) {
			$path = $this->serverParams['REQUEST_URI'];
		}

		$query = '';
		if (isset($this->serverParams['QUERY_STRING'])) {
			$query = $this->serverParams['QUERY_STRING'];
		}

		$port = 80;
		if (isset($this->serverParams['SERVER_PORT'])) {
			$port = intval($this->serverParams['SERVER_PORT']);
		}

		$fragment = '';

		$userInfo = '';
		if (isset($this->serverParams['USER'])) {
			$userInfo = intval($this->serverParams['USER']);
		}

		return new Uri($scheme, $userInfo, $host, $port, $path, $query, $fragment);
	}
}