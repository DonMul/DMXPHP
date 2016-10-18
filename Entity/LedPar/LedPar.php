<?php

namespace DMXPHP\Entity\LedPar;

use DMXPHP\Entity\Base;
use DMXPHP\IClient;

/**
 * Class LedPar
 * @package DMXPHP\Entity\LedPar
 * @author Joost Mul <joost@jmul.net>
 */
final class LedPar extends Base
{
    /**
     * LedPar constructor.
     * @param IClient $client
     * @param int     $startChannel
     */
    public function __construct(IClient $client, $startChannel)
    {
        parent::__construct($client, $startChannel, 8, 'LED Par');
    }
}