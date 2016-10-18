<?php

namespace DMXPHP\Exception;

/**
 * Class InvalidChannelValueException
 * @package DMXPHP\Exception
 * @author Joost Mul <joost@jmul.net>
 */
final class InvalidChannelValueException extends \Exception
{
    /**
     * Error code for this excpetion
     */
    const CODE = 6;

    /**
     * InvalidChannelValueException constructor.
     */
    public function __construct()
    {
        parent::__construct("Invalid channel value given.", self::CODE);
    }
}