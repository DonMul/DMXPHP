<?php

namespace DMXPHP;

use DMXPHP\Exception\InvalidChannelException;
use DMXPHP\Exception\InvalidChannelValueException;

/**
 * Interface IEntity
 * @package DMXPHP
 * @author Joost Mul <joost@jmul.net>
 */
interface IEntity
{
    /**
     * @return array
     */
    public function toArray();

    /**
     * @param int $channel
     *
     * @return boolean
     */
    public function isOnChannel($channel);

    /**
     * Loads channel values and optionally send the DMX signals
     *
     * @param int[] $channelValues  Associative array with the key being th echannel number and the value the vlaue of that channek
     * @param bool  $updateDmx      Wether or not the DMX Signal should be send after the channel has been updated on this object
     *
     * @throws InvalidChannelException      When the channel does not belong to this entity
     * @throws InvalidChannelValueException If the value of a channel exceeds the maximum value possible.
     */
    public function loadChannelValues($channelValues, $updateDmx = true);
}