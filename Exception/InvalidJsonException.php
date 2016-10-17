<?php

namespace DMXPHP\Exception;


class InvalidJsonException extends \Exception
{
    const CODE = 1;

    public function __construct()
    {
        parent::__construct("Invalid JSON string", self::CODE);
    }
}