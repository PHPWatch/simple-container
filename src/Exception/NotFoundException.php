<?php

namespace PHPWatch\SimpleContainer\Exception;

use InvalidArgumentException;
use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends InvalidArgumentException implements NotFoundExceptionInterface, SimpleContainerException {}
