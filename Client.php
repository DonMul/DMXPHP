<?php

namespace DMXPHP;

/**
 * Class Client
 * @package DMXPHP
 * @author Joost Mul <joost@jmul.net>
 */
class Client
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
     * @var array
     */
    private $dmxMap = [];

    /**
     * @var PhpSerial
     */
    private $serialInstance;

    /**
     * @var string
     */
    private $comPort = "COM1";

    /**
     * Client constructor.
     */
    public function __construct($comPort)
    {
        $this->comPort = $comPort;
        for ($i = 0; $i < self::DMX_SIZE; $i++) {
            $this->dmxMap[$i] = 0;
        }
    }


    /**
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
     * @param int $channel
     * @param int $value
     */
    public function updateChannel($channel, $value)
    {
        $this->writeToDmx($channel, $value);
    }

    /**
     * @param int $channel
     * @param int $value
     */
    private function writeToDmx($channel, $value)
    {
        $this->dmxMap[$channel] = $value;

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