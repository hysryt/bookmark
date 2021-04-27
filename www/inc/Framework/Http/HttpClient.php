<?php

namespace Hysryt\Bookmark\Framework\Http;

use Hysryt\Bookmark\Lib\HttpMessage\NetworkException;
use Hysryt\Bookmark\Lib\HttpMessage\Response;
use Hysryt\Bookmark\Lib\HttpMessage\StringStream;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpClient implements ClientInterface {
    /**
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface If an error happens while processing the request.
     */
    public function sendRequest(RequestInterface $request): ResponseInterface {
        $uri = $request->getUri();

        $context = stream_context_create(array(
            'http' => array('ignore_errors' => true)
        ));
        $body = file_get_contents((string) $uri, false, $context);
        if ($body === false) {
            throw new NetworkException($request);
        }

        $statusCode = $this->getStatusCode($http_response_header);
        $reasonPhrase = $this->getReasonPhrase($http_response_header);
        $headers = $this->getHeaders($http_response_header);
        if ($statusCode === false) {
            throw new NetworkException($request);
        }

        $bodyStream = new StringStream();
        $bodyStream->write($body);
        return new Response($statusCode, $reasonPhrase, $headers, $bodyStream);
    }

    /**
     * 直近のリクエストで帰ってきたステータスコードを取得
     * 取得できない場合は false を返す
     * 
     * @return int|false
     */
    private function getStatusCode(array $http_response_header) {
        if (isset($http_response_header[0])) {
            $regexp = '/^HTTP\/[0-9\.]+ ([0-9]{3}) (.+)$/';
            preg_match($regexp, $http_response_header[0], $matches);

            if (isset($matches[1]) && is_numeric($matches[1])) {
                return intval($matches[1]);
            }
        }
        return false;
    }

    /**
     * 直近のリクエストで帰ってきたreason-phraseを返す
     * 取得できない場合は false を返す
     * 
     * @return string|false
     */
    private function getReasonPhrase(array $http_response_header) {
        if (isset($http_response_header[0])) {
            $regexp = '/^HTTP\/[0-9\.]+ ([0-9]{3}) (.+)$/';
            preg_match($regexp, $http_response_header[0], $matches);

            if (isset($matches[2])) {
                return $matches[2];
            }
        }
        return false;
    }

    /**
     * 直近のリクエストで帰ってきたヘッダーを返す
     * 
     * @return array - { 'ヘッダー名' => 'ヘッダー値 } の連想配列
     */
    private function getHeaders(array $http_response_header): array {
        $headers = [];
        if (isset($http_respose_header) && is_array($http_response_header)) {
            foreach ($http_response_header as $line) {
                $commaPos = strpos($line, ':');
                if ($commaPos !== false) {
                    $key = trim(substr($line, 0, $commaPos));
                    $value = trim(substr($line, $commaPos));
                    $headers[$key] = $value;
                }
            }
        }
        return $headers;
    }
}