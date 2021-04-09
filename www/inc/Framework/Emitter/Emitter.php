<?php

namespace Hysryt\Bookmark\Framework\Emitter;

use Psr\Http\Message\ResponseInterface;

class Emitter implements EmitterInterface {
    public function emit(ResponseInterface $response) {
        // TODO: headers
        echo $response->getBody()->getContents();
    }
}