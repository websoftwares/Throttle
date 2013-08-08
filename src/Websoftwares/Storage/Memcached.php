<?php
namespace Websoftwares\Storage;
/**
 * Memcached
 * Class for handling memcached storage
 *
 * @package Websoftwares
 * @subpackage Storage
 * @license http://www.dbad-license.org/ DbaD
 * @version 0.1
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
     * @param $ip adress to save
     * @param $amount the current amount
     * @param $timespan the timespan in seconds
     *
     * @return boolean
     */
    public function save($ip = null, $amount = null, $timespan =  null)
    {
        if (!$ip && !$amount && !$timespan) {
            throw new \InvalidArgumentException('ip, amount and timespan are required');
        }

        return $this->memcached->set($this->fileName($ip), $amount, $timespan);
    }

    /**
     * increment
     *
     * @param int $ip adress to increment the value for
     *
     * @return boolean/int
     */
    public function increment($ip = null)
    {
        if (!$ip) {
            throw new \InvalidArgumentException('ip is a required argument');
        }

        return $this->memcached->increment($this->fileName($ip));
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
