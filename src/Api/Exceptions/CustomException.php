<?php

declare(strict_types=1);

namespace KyleWLawrence\Infinity\Api\Exceptions;

/**
 * CustomException extends the Exception class with simplified messaging
 */
class CustomException extends \Exception
{
    /**
     * @param  string  $message
     * @param  int  $code
     * @param  \Exception  $previous
     */
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
