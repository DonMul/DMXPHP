<?php

namespace DMXPHP\Exception;

/**
 * Class InvalidEntityException
 * @package DMXPHP\Exception
 * @author Joost Mul <joost@jmul.net>
 */
final class InvalidEntityException extends \Exception
{
    /**
     * Error code for this Exception
     */
    const CODE = 5;

    /**
     * InvalidEntityException constructor.
     */
    public function __construct()
    {
        parent::__construct("Invalid entity given.", self::CODE);
    }
}