<?php

namespace DMXPHP;

/**
 * Interface which all DMX clients should implement
 *
 * @package DMXPHP
 * @author Joost Mul <joost@jmul.net>
 */
interface IClient
{
    /**
     * Updates the given channel to the given value
     *
     * @param int $channel The channel to update
     * @param int $value   The value to set the channel to
     */
    public function updateChannel($channel, $value);
}