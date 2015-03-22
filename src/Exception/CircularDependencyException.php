<?php

namespace Kampaw\Dic\Exception;

class CircularDependencyException extends \LogicException implements ExceptionInterface
{
}