<?php

namespace Hysryt\Bookmark\Framework\Http;

use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;
use RuntimeException;
use Throwable;

/**
 * Thrown when the request cannot be completed because of network issues.
 *
 * There is no response object as this exception is thrown when no response has been received.
 *
 * Example: the target host name can not be resolved or the connection failed.
 */
class NetworkException extends RuntimeException implements NetworkExceptionInterface {
    private RequestInterface $request;

    public function __construct(RequestInterface $request, $message = null, $code = 0, Throwable $previous = null) {
        $this->request = $request;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Returns the request.
     *
     * The request object MAY be a different object from the one passed to ClientInterface::sendRequest()
     *
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface {
        return $this->request;
    }
}