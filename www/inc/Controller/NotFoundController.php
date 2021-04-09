<?php

namespace Hysryt\Bookmark\Controller;

use Hysryt\Bookmark\Framework\Http\Response;
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