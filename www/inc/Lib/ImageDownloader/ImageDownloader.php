<?php

namespace Hysryt\Bookmark\Lib\ImageDownloader;

use Hysryt\Bookmark\Lib\Image\Image;
use Hysryt\Bookmark\Lib\Image\ImageFactory;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

class ImageDownloader {
    private ClientInterface $client;
    private RequestFactoryInterface $requestFactory;

    public function __construct(ClientInterface $client, RequestFactoryInterface $requestFactory) {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
    }

    /**
     * @throws ImageDownloaderException;
     */
    public function download(string $url): Image {
        $imageString = $this->downloadImageString($url);
        return ImageFactory::fromString($imageString);
    }

    /**
     * @throws ImageDownloaderException;
     */
    private function downloadImageString(string $url): string {
        $request = $this->requestFactory->createRequest('GET', $url);
        $response = $this->client->sendRequest($request);
        if ($response->getStatusCode() !== 200) {
            throw new ImageDownloaderException('network error');
        }
        return $response->getBody()->getContents();
    }
}