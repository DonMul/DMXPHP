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
     * @var array
     */
    private $dmxMap = [];

    /**
     * @var PhpSerial
     */
    private $serialInstance;

    /**
     * @var Client
     */
    private static $instance;

    /**
     * Client constructor.
     */
    private function __construct()
    {
        for ($i = 0; $i < self::DMX_SIZE; $i++) {
            $this->dmxMap[$i] = 0;
        }
    }

    /**
     * @return Client
     */
    public static function getInstance()
    {
        if (!(self::$instance instanceof Client)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return PhpSerial
     */
    private function getSerialWriter()
    {
        if (!($this->serialInstance instanceof PhpSerial)) {
            $this->serialInstance = new PhpSerial();
            $this->serialInstance->deviceSet(Settings::$comPort);
            $this->serialInstance->confBaudRate(Settings::$baudRate);

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
     *
     */
    public function __destruct()
    {
        if (($this->serialInstance instanceof PhpSerial)) {
            $this->serialInstance->deviceClose();
        }
    }
}