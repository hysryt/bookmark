<?php

namespace Hysryt\Bookmark\Lib\HttpClient;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class Client implements ClientInterface {
    private ResponseFactoryInterface $responseFactory;
    private array $options;

    private array $defaultOptions = [
        'timeout' => 60,
    ];

    /**
     * コンストラクタ
     * 
     * @param ResponseFactoryInterface $responseFactory
     */
    public function __construct(ResponseFactoryInterface $responseFactory, array $options = []) {
        $this->responseFactory = $responseFactory;
        $this->options = array_merge($this->defaultOptions, $options);
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
        // リクエスト実行
        $ch = curl_init($request->getUri());
        curl_setopt_array($ch, [
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->options['timeout'],
        ]);
        $output = curl_exec($ch);
        if ($output === false) {
            // タイムアウトなど
            throw new NetworkException($request);
        }

        // ステータスコード取得
        $statusCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

        // ヘッダーとボディを取得
        $headers = $this->parseHeaders($output, $ch);
        $body = $this->parseBody($output, $ch);
        curl_close($ch);

        // レスポンス生成
        $response = $this->responseFactory->createResponse($statusCode);
        $response->getBody()->write($body);
        foreach($headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        return $response;
    }

    /**
     * ヘッダーを取得
     * 
     * @param string $output
     * @param $ch
     * @return array<string, array<string>>
     */
    private function parseHeaders(string $output, $ch): array {
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headerPart = substr($output, 0, $headerSize);
        $headerLines = explode("\r\n", $headerPart);
        return $this->convertHeaderLinesToArray($headerLines);
    }

    /**
     * ヘッダー行の配列を、array<ヘッダー名, array<ヘッダー値>>の配列に変換する
     * 
     * @param array
     * @return array
     */
    private function convertHeaderLinesToArray(array $headerLines) {
        $headers = [];
        foreach($headerLines as $line) {
            $splitPos = strpos($line, ':');
            if ($splitPos !== false) {
                $name = trim(substr($line, 0, $splitPos));
                $value = trim(substr($line, $splitPos + 1));
                if (!isset($headers[$name])) {
                    $headers[$name] = [];
                }
                $headers[$name][] = $value;
            }
        }
        return $headers;
    }

    /**
     * ボディを取得
     * 
     * @param string $output
     * @param $ch
     * @return string
     */
    private function parseBody(string $output, $ch): string {
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $bodyPart = substr($output, $headerSize);
        return $bodyPart;
    }
}