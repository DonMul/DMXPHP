<?php

namespace DMXPHP\Client;

use DMXPHP\IClient;
use DMXPHP\PhpSerial;

/**
 * The client used to send the DMX signals with. This client should'nt be used directly. Use the Entity and universe
 * objects insted if you want to control your DMX devices.
 *
 * @package DMXPHP
 * @author Joost Mul <joost@jmul.net>
 */
class USB implements IClient
{
    /**
     * Start of a DMX message
     */
    const START_VAL = 0x7E;

    /**
     * End of a DMX message
     */
    const END_VAL = 0XE7;

    /**
     * Start of the DMX packet in the message
     */
    const DMX_PACKET = 6;

    /**
     * Max size of a DMX universe
     */
    const DMX_SIZE = 512;

    /**
     * BAUD rate
     */
    const BAUDRATE = 57600;

    /**
     * Array containing all 512 channels and all of their current values.
     *
     * @var array
     */
    private $dmxMap = [];

    /**
     * Used to write to a Serial port.
     *
     * @var PhpSerial
     */
    private $serialInstance;

    /**
     * The port that the DMX USB device is connected to.
     *
     * @var string
     */
    private $comPort = "COM1";

    /**
     * USB Client constructor.
     *
     * @param string $comPort The COMport the DMX USB Device is connected to
     */
    public function __construct($comPort)
    {
        $this->comPort = $comPort;

        for ($i = 0; $i < self::DMX_SIZE; $i++) {
            $this->dmxMap[$i] = 0;
        }
    }


    /**
     * Returns the serial writer for the DMX connection.
     *
     * @return PhpSerial
     */
    private function getSerialWriter()
    {
        if (!($this->serialInstance instanceof PhpSerial)) {
            $this->serialInstance = new PhpSerial();
            $this->serialInstance->deviceSet($this->comPort);
            $this->serialInstance->confBaudRate(self::BAUDRATE);

            $this->serialInstance->deviceOpen();
        }

        return $this->serialInstance;
    }

    /**
     * Updates the given channel to the given value.
     *
     * @param int $channel The channel to update.
     * @param int $value   The value to set the channel to.
     */
    public function updateChannel($channel, $value)
    {
        $this->dmxMap[$channel] = $value;
        $this->writeToDmx();
    }

    public function updateMultipleChannels($channelValues)
    {
        foreach ($channelValues as $channel => $value) {
            $this->dmxMap[$channel] = $value;
        }

        $this->writeToDmx();
    }

    /**
     * Send the current DMX map to the physical DMX universe
     */
    private function writeToDmx()
    {
        $packet = [
            self::START_VAL,
            self::DMX_PACKET,
            count($this->dmxMap) & 0xFF,
            (count($this->dmxMap) >> 8) & 0xFF,
        ];

        foreach ($this->dmxMap as $channel => $value) {
            $packet[] = $value;
        }

        $packet[] = self::END_VAL;

        $chrs = array_map(function($a){
            return chr($a);
        }, $packet);

        $this->getSerialWriter()->sendMessage(implode('', $chrs));
    }

    /**
     * Ensures the connection to the COMport is closed when this class is destructed.
     */
    public function __destruct()
    {
        if (($this->serialInstance instanceof PhpSerial)) {
            $this->serialInstance->deviceClose();
        }
    }
}