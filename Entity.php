<?php

namespace DMXPHP;

/**
 * Class Entity
 * @package DMXPHP
 * @author Joost Mul <joost@jmul.net>
 */
class Entity
{
    /**
     * @var string
     */
    private $name = '';

    /**
     * @var int
     */
    private $channels = 0;

    /**
     * @var int
     */
    private $startChannel = 0;

    /**
     * @var int[]
     */
    private $channelValues = [];

    /**
     * Entity constructor.
     * @param int    $startChannel
     * @param int    $channels
     * @param string $name
     */
    public function __construct($startChannel, $channels, $name = '')
    {
        $this->startChannel = $startChannel;
        $this->channels = $channels;
        $this->name = $name;

        for ($i = 0; $i < $channels; $i++) {
            $this->channelValues[$this->startChannel + $i] = 0;
        }
    }

    /**
     * @param int $channel
     * @param int $value
     * @throws \Exception
     */
    public function updateChannel($channel, $value)
    {
        if (!isset($this->channelValues[$channel])) {
            $channel = $this->startChannel + $channel;
            if (!isset($this->channelValues[$channel])) {
                throw new \Exception("Invalid channel given");
            }
        }

        $this->channelValues[$channel] = $value;
        Client::getInstance()->updateChannel($channel, $value);
    }
}