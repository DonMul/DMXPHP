<?php

namespace DMXPHP;
use DMXPHP\Exception\InvalidChannelException;

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
     * @var Client
     */
    private $client;

    /**
     * Entity constructor.
     *
     * @param Client $client
     * @param int    $startChannel
     * @param int    $channels
     * @param string $name
     */
    public function __construct(Client $client, $startChannel, $channels, $name = '')
    {
        $this->client = $client;
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
     */
    public function updateChannel($channel, $value)
    {
        $this->loadChannelValues([$channel => $value], true);
    }

    /**
     * @param int $channel
     * @return bool
     */
    public function isOnChannel($channel)
    {
        return isset($this->channelValues[$channel]);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'startChannel' => $this->startChannel,
            'channels' => $this->channels,
            'name' => $this->name,
            'channelValues' => $this->channelValues,
        ];
    }

    /**
     * @param int[] $channelValues
     * @param bool  $updateDmx
     * @throws InvalidChannelException
     */
    public function loadChannelValues($channelValues, $updateDmx = true)
    {
        foreach ($channelValues as $channel => $value) {
            if (!isset($this->channelValues[$channel])) {
                $channel = $this->startChannel + $channel;
                if (!isset($this->channelValues[$channel])) {
                    throw new InvalidChannelException();
                }
            }

            $this->channelValues[$channel] = $value;
            if ($updateDmx === true) {
                $this->client->updateChannel($channel, $value);
            }
        }
    }
}