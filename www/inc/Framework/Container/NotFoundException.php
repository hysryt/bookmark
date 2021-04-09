<?php

namespace Hysryt\Bookmark\Framework\Container;

use LogicException;
use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends LogicException implements NotFoundExceptionInterface {

}