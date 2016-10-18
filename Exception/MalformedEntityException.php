<?php

namespace DMXPHP\Exception;

/**
 * Class MalformedEntityException
 * @package DMXPHP\Exception
 * @author Joost Mul <joost@jmul.net>
 */
class MalformedEntityException extends \Exception
{
    /**
     * Error code for this exception
     */
    const CODE = 2;

    /**
     * MalformedEntityException constructor.
     */
    public function __construct()
    {
        parent::__construct("Entity is malformed", self::CODE);
    }
}