<?php

namespace DMXPHP\Exception;

/**
 * Class InvalidJsonException
 * @package DMXPHP\Exception
 * @author Joost Mul <joost@jmul.net>
 */
final class InvalidJsonException extends \Exception
{
    /**
     * Error code for this exception
     */
    const CODE = 1;

    /**
     * InvalidJsonException constructor.
     */
    public function __construct()
    {
        parent::__construct("Invalid JSON string", self::CODE);
    }
}