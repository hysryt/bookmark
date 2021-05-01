<?php

namespace Hysryt\Bookmark\Lib\HttpClient;

use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;
use RuntimeException;
use Throwable;

/**
 * リクエストが失敗したときの例外。
 *
 * 例:
 *      - リクエストが無効（例：メソッド（GETやPOST）がない）
 *      - 実行時のリクエストエラー（例：ボディストリームがシークできない）
 */
class RequestException extends RuntimeException implements RequestExceptionInterface {
    private RequestInterface $request;

    public function __construct(RequestInterface $request, $message = null, $code = 0, Throwable $previous = null) {
        $this->request = $request;
        parent::__construct($message, $code, $previous);
    }

    /**
     * リクエストを返します。
     *
     * リクエストオブジェクトは、ClientInterface::sendRequest()に渡されたものとは異なるオブジェクトであってもかまいません。
     *
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface {
        return $this->request;
    }
}