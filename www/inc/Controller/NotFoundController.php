<?php

namespace Hysryt\Bookmark\Controller;

use Hysryt\Bookmark\Lib\HttpMessage\Response;
use Psr\Http\Message\ServerRequestInterface;

class NotFoundController {
	/**
	 * @param ServerRequestInterface $request
	 * @return Response
	 */
	public function do(ServerRequestInterface $request) {
		return Response::notFound();
	}
}