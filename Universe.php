<?php

namespace DMXPHP;

use DMXPHP\Exception\EntityNotFoundException;
use DMXPHP\Exception\InvalidJsonException;
use DMXPHP\Exception\MalformedEntityException;

/**
 * Class Universe.
 *
 * @package DMXPHP
 * @author Joost Mul <joost@jmul.net>
 */
class Universe
{
    /**
     * @var Entity[]
     */
    private $entities = [];

    /**
     * @var Client
     */
    private $client;

    /**
     * Universe constructor.
     *
     * @param Entity[] $entities
     */
    public function __construct($entities, Client $client)
    {
        $this->client = $client;

        foreach ($entities as $entity) {
            if (!($entity instanceof Entity)) {
                continue;
            }

            $this->entities[] = $entity;
        }
    }

    /**
     * @param int $channel
     * @return Entity
     * @throws EntityNotFoundException
     */
    public function getEntityOnChannel($channel)
    {
        foreach ($this->entities as &$entity) {
            if ($entity->isOnChannel($channel)) {
                return $entity;
            }
        }

        throw new EntityNotFoundException();
    }

    /**
     * @param string $string
     * @return Universe
     * @throws InvalidJsonException
     */
    public static function createFromJsonString($string, Client $client)
    {
        $decoded = @json_decode($string);
        if (!$decoded) {
            throw new InvalidJsonException();
        }

        return self::createFromArray($decoded, $client);
    }

    /**
     * @param array $array
     * @return Universe
     * @throws MalformedEntityException
     */
    public static function createFromArray($array, Client $client)
    {
        $entities = [];
        foreach ($array as $entity) {
            if (!isset($entity['startChannel'])) {
                throw new MalformedEntityException();
            }

            $entityObj = new Entity(
                $client,
                $entity['startChannel'],
                $entity['channels'],
                isset($entity['name']) ? $entity['name'] : ''
            );

            if (isset($entity['channelValues'])) {
                $entityObj->loadChannelValues($entity['channelValues'], false);
            }

            $entities[] = $entityObj;
        }

        return new self($entities, $client);
    }

    /**
     * @param $universeName
     * @return Universe|null
     */
    public static function createFromCache($universeName, Client $client)
    {
        $cacheName = self::getCacheFileLocation($universeName);
        if (!file_exists($cacheName)) {
            return null;
        }

        return self::createFromArray(unserialize(file_get_contents($cacheName)), $client);
    }

    /**
     * @param string $universeName
     */
    public function storeInCache($universeName)
    {
        file_put_contents(
            self::getCacheFileLocation($universeName),
            serialize($this->getEntitiesArray())
        );
    }

    /**
     * @return array
     */
    public function getEntitiesArray()
    {
        $entities = [];
        foreach ($this->entities as $entity) {
            $entities[] = $entity->toArray();
        }

        return $entities;
    }

    /**
     * Returns the cache file location for the given universe name.
     *
     * @param string $universeName
     * @return string
     */
    private static function getCacheFileLocation($universeName)
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . md5($universeName) . '.cache';
    }
}