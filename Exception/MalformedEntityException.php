<?php

namespace DMXPHP\Exception;


class MalformedEntityException extends \Exception
{
    const CODE = 2;

    public function __construct()
    {
        parent::__construct("Entity is malformed", self::CODE);
    }
}