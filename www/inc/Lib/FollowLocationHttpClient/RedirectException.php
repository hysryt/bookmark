<?php

namespace Hysryt\Bookmark\Lib\FollowLocationHttpClient;

use Psr\Http\Client\ClientExceptionInterface;
use RuntimeException;

class RedirectException extends RuntimeException implements ClientExceptionInterface {
    const TOO_MANY_REDIRECTS = 0;
    const INVALID_REDIRECT_URL = 1;
    const NOT_PROVIDED_REDIRECT_URL = 2;
}