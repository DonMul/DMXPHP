<?php

namespace DMXPHP\Exception;

/**
 * Class EntityNotFoundException
 * @package DMXPHP\Exception
 * @author Joost Mul <joost@jmul.net>
 */
class EntityNotFoundException extends \Exception
{
    /**
     * Error codoe for this Exception
     */
    const CODE = 3;

    /**
     * EntityNotFoundException constructor.
     */
    public function __construct()
    {
        parent::__construct("Entity not found", self::CODE);
    }
}