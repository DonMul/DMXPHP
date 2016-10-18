<?php

namespace DMXPHP\Entity;

use DMXPHP\Exception\InvalidChannelException;
use DMXPHP\Exception\InvalidChannelValueException;
use DMXPHP\IClient;
use DMXPHP\IEntity;

/**
 * An entity within the DMX Universe. An entity could span over multiple channels
 *
 * @package DMXPHP
 * @author Joost Mul <joost@jmul.net>
 */
abstract class Base implements IEntity
{
    /**
     * The maximum value of a channel on DMX
     */
    const MAX_CHANNEL_VALUE = 255;

    /**
     * Name of this entity
     *
     * @var string
     */
    private $name = '';

    /**
     * The amount of channels this entity covers
     *
     * @var int
     */
    private $channels = 0;

    /**
     * The number of the first channel this entity listens on
     *
     * @var int
     */
    private $startChannel = 0;

    /**
     * An associative array containing all values of all channels
     *
     * @var int[]
     */
    private $channelValues = [];

    /**
     * The client used to send DMX signals with
     *
     * @var IClient
     */
    private $client;

    /**
     * Entity constructor.
     *
     * @param IClient $client        The client used to send DMX signals with
     * @param int    $startChannel  First channel this entity listens on
     * @param int    $channels      The amount of channels this entity listens on
     * @param string $name          Optional name for this entity
     */
    public function __construct(IClient $client, $startChannel, $channels, $name = '')
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
     * Update the given channel to the given value
     *
     * @param int $channel The channel number that needs to be updated
     * @param int $value   The value the channel should be set to
     */
    public function updateChannel($channel, $value)
    {
        $this->loadChannelValues([$channel => $value], true);
    }

    /**
     * Returns wether or not this entity is listening on the given channel
     *
     * @param int $channel The channel to check for
     *
     * @return bool Whether or not this entity is listening on the given channel
     */
    public function isOnChannel($channel)
    {
        return isset($this->channelValues[$channel]);
    }

    /**
     * Transforms this object to an associative array and returns that array
     *
     * @return array Array containing data about this entity
     */
    public function toArray()
    {
        return [
            'startChannel' => $this->startChannel,
            'channels' => $this->channels,
            'name' => $this->name,
            'channelValues' => $this->channelValues,
            'type' => get_called_class(),
        ];
    }

    /**
     * Loads channel values and optionally send the DMX signals
     *
     * @param int[] $channelValues  Associative array with the key being th echannel number and the value the vlaue of that channek
     * @param bool  $updateDmx      Wether or not the DMX Signal should be send after the channel has been updated on this object
     *
     * @throws InvalidChannelException      When the channel does not belong to this entity
     * @throws InvalidChannelValueException If the value of a channel exceeds the maximum value possible.
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

            if ($value <= self::MAX_CHANNEL_VALUE) {
                throw new InvalidChannelValueException();
            }

            $this->channelValues[$channel] = $value;

            if ($updateDmx === true) {
                $this->client->updateChannel($channel, $value);
            }
        }
    }
}