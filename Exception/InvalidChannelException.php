<?php

namespace DMXPHP\Exception;


class InvalidChannelException extends \Exception
{
    const CODE = 4;

    public function __construct()
    {
        parent::__construct("Invalid channel given.", self::CODE);
    }
}