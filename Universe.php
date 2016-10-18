<?php

namespace DMXPHP;

use DMXPHP\Exception\EntityNotFoundException;
use DMXPHP\Exception\InvalidEntityException;
use DMXPHP\Exception\InvalidJsonException;
use DMXPHP\Exception\MalformedEntityException;
use DMXPHP\Entity\Base;

/**
 * Class representing a DMX Universe (with a max of 512 channels)
 *
 * @package DMXPHP
 * @author Joost Mul <joost@jmul.net>
 */
final class Universe
{
    /**
     * An array of entities within this universe
     *
     * @var IEntity[]
     */
    private $entities = [];

    /**
     * IClient used to interact with an USB interface in order to send the DMX signals
     *
     * @var IClient
     */
    private $client;

    /**
     * Creator of a DMX Universe
     *
     * @param IEntity[]  $entities   All DMX entities within this universe. These can also be added later on
     * @param IClient    $client     The client used to send DMX signals with
     */
    public function __construct($entities, IClient $client)
    {
        $this->client = $client;

        foreach ($entities as $entity) {
            $this->addEntity($entity);
        }
    }

    /**
     * Adds an Entity to this universe
     *
     * @param IEntity $entity The entity to add
     */
    public function addEntity(IEntity $entity)
    {
        $this->entities[] = $entity;
    }

    /**
     * Returns the entity that is registered on the given channel.
     *
     * @param int $channel The entity listening on this channel
     *
     * @return IEntity The entity listening on the given channel
     *
     * @throws EntityNotFoundException When the entity is not found
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
     * Creates a DMX Universe based on the given JSON string. This JSON string should be the json encoded array containing
     * all the entities within the universe.
     *
     * @param string $string The JSON string containing the entities
     * @param IClient $client The client used to send DMX signals with
     *
     * @return Universe The newly created universe, containing the given entities
     *
     * @throws InvalidJsonException If the string is invalid JSON
     */
    public static function createFromJsonString($string, IClient $client)
    {
        $decoded = @json_decode($string, true);
        if (!$decoded) {
            throw new InvalidJsonException();
        }

        return self::createFromArray($decoded, $client);
    }

    /**
     * Creates a DMX universe from the given array. This array should contain all entities within the universe.
     *
     * @param array  $array  Array with all information about entities within the newly created universe
     * @param IClient $client The client used to send DMX signals with
     *
     * @return Universe The newly created universe, containing all given entities.
     *
     * @throws MalformedEntityException When an entry in the given $array is missing its required information.
     */
    public static function createFromArray($array, IClient $client)
    {
        $entities = [];
        $universe = new self([], $client);
        foreach ($array as $entity) {
            if (!isset($entity['startChannel'])) {
                throw new MalformedEntityException();
            }

            $type = isset($entity['type']) ? $entity['type'] : '';

            /**
             * @type IEntity $entityObj
             */
            $entityObj = new $type(
                $client,
                $entity['startChannel'],
                $entity['channels'],
                isset($entity['name']) ? $entity['name'] : ''
            );

            if (isset($entity['channelValues'])) {
                $entityObj->loadChannelValues($entity['channelValues'], false);
            }

            $universe->addEntity($entityObj);
        }

        return $universe;
    }

    /**
     * Retrieve a DMX Universe from cache.
     *
     * @param string $universeName The name of the universe that was cached earlier on. This name is also used as cache key
     * @param IClient $client       The client used to send DMX signals with
     *
     * @return Universe The universe retrieved from Cache
     */
    public static function createFromCache($universeName, IClient $client)
    {
        $cacheName = self::getCacheFileLocation($universeName);
        if (!file_exists($cacheName)) {
            return null;
        }

        return self::createFromArray(unserialize(file_get_contents($cacheName)), $client);
    }

    /**
     * Stores this Universe Object to cache for later retrieval
     *
     * @param string $universeName The name of the universe that needs to be cached. This will be used as cachekey
     */
    public function storeInCache($universeName)
    {
        file_put_contents(
            self::getCacheFileLocation($universeName),
            serialize($this->getEntitiesArray())
        );
    }

    /**
     * Returns all entities as an array
     *
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
     * @param string $universeName The name of the universe the cache file location should be generated for
     *
     * @return string
     */
    private static function getCacheFileLocation($universeName)
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . md5($universeName) . '.cache';
    }
}