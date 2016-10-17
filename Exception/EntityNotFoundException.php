<?php

namespace DMXPHP\Exception;


class EntityNotFoundException extends \Exception
{
    const CODE = 3;

    public function __construct()
    {
        parent::__construct("Entity not found", self::CODE);
    }
}