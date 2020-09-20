<?php

namespace ialopezg\Exceptions;

/**
 * Exception thrown when an argument is missing or its value were not specified.
 *
 * @package ialopezg\Exceptions
 */
class MissingArgumentException extends \InvalidArgumentException {
    /**
     * MissingArgumentException constructor.
     *
     * @param string $paramName parameter missing.
     * @param string $class class that thrown the exception, if any.
     * @param string $method method that thrown the exception, if any.
     */
    public function __construct($paramName = '', $class = '', $method = '') {
        if ($class && $method) {
            parent::__construct("{$class}->{$method}() cannot be configure. Missing '{$paramName}' argument.");
        } elseif (!$class && $method) {
            parent::__construct("{$method}() cannot be configure. Missing '{$paramName}' argument.");
        } else {
            parent::__construct("Missing '{$paramName}' argument.");
        }
    }
}