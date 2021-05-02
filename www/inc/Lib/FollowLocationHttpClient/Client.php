<?php

namespace Hysryt\Bookmark\Lib\FollowLocationHttpClient;

use Hysryt\Bookmark\Lib\HttpMessage\Uri;
use InvalidArgumentException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class Client implements ClientInterface {
    private ClientInterface $client;
    private int $maxRedirect;


    /**
     * コンストラクタ
     * 
     * @param ResponseFactoryInterface $responseFactory
     */
    public function __construct(ClientInterface $client, int $maxRedirect = 10) {
        $this->client = $client;
        $this->maxRedirect = $maxRedirect;
    }

    /**
     * PSR-7リクエストを送信し、PSR-7レスポンスを返信します。
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface リクエストの処理中にエラーが発生した場合
     */
    public function sendRequest(RequestInterface $request): ResponseInterface {
        $redirectCount = 0;

        while(true) {
            // リクエストを送信し、レスポンスがリダイレクト以外であれば呼び出し元に返す
            $response = $this->client->sendRequest($request);
            if (!$this->isRedirectResponse($response)) {
                return $response;
            }

            // 最大リダイレクト回数の確認
            $redirectCount++;
            if ($redirectCount > $this->maxRedirect) {
                throw new RedirectException('リダイレクトが多すぎます', RedirectException::TOO_MANY_REDIRECTS);
            }

            // リダイレクト先へのリクエスト生成
            $request = $this->createRedirectRequest($request, $response);
        }
    }

    private function isRedirectResponse(ResponseInterface $response): bool {
        return in_array($response->getStatusCode(), [301, 302]);
    }

    /**
     * リダイレクト先へのPSR-7リクエストを生成
     * 
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return RequestInterface
     * @throws RedirectException
     */
    private function createRedirectRequest(RequestInterface $request, ResponseInterface $response): RequestInterface {
        if (!$response->hasHeader('location') || !isset($response->getHeader('location')[0])) {
            throw new RedirectException('リダイレクト先のURLがありません', RedirectException::NOT_PROVIDED_REDIRECT_URL);
        }

        try {
            $redirectTo = Uri::createFromUriString($response->getHeader('location')[0]);
        } catch(InvalidArgumentException $e) {
            throw new RedirectException('リダイレクト先URLが不正です', RedirectException::INVALID_REDIRECT_URL, $e);
        }
        
        return $request->withUri($redirectTo);
    }
}