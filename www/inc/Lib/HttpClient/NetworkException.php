<?php

namespace Hysryt\Bookmark\Lib\HttpClient;

use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;
use RuntimeException;
use Throwable;

/**
 * ネットワークの問題でリクエストが完了できない場合にスローされます。
 *
 * レスポンスを受信していない場合にこの例外がスローされるため、レスポンスオブジェクトはありません。
 *
 * 例：ターゲットのホスト名が解決できない、または接続に失敗した。
 */
class NetworkException extends RuntimeException implements NetworkExceptionInterface {
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