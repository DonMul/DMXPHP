<?php

namespace DMXPHP\Exception;

/**
 * Class InvalidChannelException
 * @package DMXPHP\Exception
 * @author Joost Mul <joost@jmul.net>
 */
final class InvalidChannelException extends \Exception
{
    /**
     * Error code for this exceptoin
     */
    const CODE = 4;

    /**
     * InvalidChannelException constructor.
     */
    public function __construct()
    {
        parent::__construct("Invalid channel given.", self::CODE);
    }
}