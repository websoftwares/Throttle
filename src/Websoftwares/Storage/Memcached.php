<?php
namespace Websoftwares\Storage;
/**
 * Memcached
 * Class for handling memcached storage
 *
 * @package Websoftwares
 * @subpackage Storage
 * @license http://www.dbad-license.org/ DbaD
 * @version 0.3
 * @author Boris <boris@websoftwar.es>
 */
class Memcached implements \Websoftwares\StorageInterface
{
    /**
     * $memcached
     * @var Memcached
     */
    protected $memcached = null;

    /**
     * __construct
     * @param array $servers provide a list of memcached servers
     */
    public function __construct(array $servers = array())
    {
        // Servers list
        $servers = array_merge(array('localhost' => 11211),$servers);
        $memcached = new \Memcached();

        // Setup memcached servers
        foreach ($servers as $server => $port) {
            $memcached->addServer($server, $port);
        }

        // Assign to property
        $this->memcached = $memcached;
    }

    /**
     * save
     *
     * @param mxied  $identifier identifier to save
     * @param string $amount     the current amount
     * @param string $timespan   the timespan in seconds
     *
     * @return boolean
     */
    public function save($identifier = null, $amount = null, $timespan =  null)
    {
        if (!$identifier && !$amount && !$timespan) {
            throw new \InvalidArgumentException('identifier, amount and timespan are required');
        }

        return $this->memcached->set($this->fileName($identifier), $amount, $timespan);
    }

    /**
     * increment
     *
     * @param mixed $identifier identifier to increment the value for
     *
     * @return boolean/int
     */
    public function increment($identifier = null)
    {
        if (!$identifier) {
            throw new \InvalidArgumentException('identifier is a required argument');
        }

        return $this->memcached->increment($this->fileName($identifier));
    }

    /**
     * get
     *
     * @param mixed $identifier identifier to retrieve the value from storage
     *
     * @return mixed boolean
     */
    public function get($identifier = null)
    {
        if (!$identifier) {
            throw new \InvalidArgumentException('identifier is a required argument');
        }

        return $this->memcached->get($this->fileName($identifier));
    }

    /**
     * delete
     *
     * @param mixed $identifier identifier to delete the entry for
     *
     * @return boolean
     */
    public function delete($identifier = null)
    {
        if (!$identifier) {
            throw new \InvalidArgumentException('identifier is a required argument');
        }

        return $this->memcached->delete($this->fileName($identifier));
    }

    /**
     * fileName transform key into a md5 hashed string
     *
     * @param  string $key
     * @return string
     */
    private function fileName($key)
    {
        return md5($key);
    }
}
