<?php

namespace Hysryt\Bookmark\Framework\Emitter;

use Psr\Http\Message\ResponseInterface;

interface EmitterInterface {
    public function emit(ResponseInterface $response);
}