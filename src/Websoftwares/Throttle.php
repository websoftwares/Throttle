<?php
namespace Websoftwares;
use Psr\Log\LoggerInterface, Websoftwares\StorageInterface;
/**
 * Throttle class
 * Ban identifier after certain amount of requests in a given timeframe.
 *
 * Converted from python example and comments from below link.
 * @link https://forrst.com/posts/Limiting_number_of_requests_in_a_given_timeframe-0BW
 *
 * @package Websoftwares
 * @license http://www.dbad-license.org/ DbaD
 * @version 0.2
 * @author Boris <boris@websoftwar.es>
 */
class Throttle
{
    /**
     * $logger object that implements the PSR-3 LoggerInterface
     * @var object
     */
    private $logger = null;
    /**
     * $storage object that implements the storage interface
     * @var object
     */
    private $storage = null;
    /**
     * $options the options that are required
     * @var array
     */
    private $options = array();

    /**
     * __construct
     * @param object $logger  object that implements the PSR-3 LoggerInterface
     * @param object $storage object that implements the StorageInterface
     * @param array  $options array('banned' => 5, 'logged' => 10, 'timespan' => '86400')
     */
    public function __construct(LoggerInterface $logger = null, StorageInterface $storage = null, array $options = array())
    {
        // logger and storage are required
        if (! $logger && ! $storage) {
            throw new \InvalidArgumentException('logger and storage objects are required');
        }

        // Assign objects to properties
        $this->logger = $logger;
        $this->storage = $storage;

        // The options
        $this->options = array_merge(
            array(
                'banned' => 5, // Ban identifier after 5 attempts
                'logged' => 10, // Log identifier after 10 attempts
                'timespan' => '86400' // The timespan in seconds for ban
                ),
            $options
        );
    }

    /**
     * validate
     *
     * @param  mixed   $identifier
     * @return boolean
     */
    public function validate($identifier = null)
    {
        // No identifier
        if (!$identifier) {
            throw new \InvalidArgumentException('identifier is a required argument');
        }

        // Current attempts
        $attempts = $this->storage->increment($identifier);

        // No attempts
        if (! $attempts) {
            $this->storage->save($identifier, 1, $this->options['timespan']);

        // Logged
        } elseif ($attempts == $this->options['logged']) {
            $this->logger->log('warning', $identifier . ' exceeded the number of allowed requests');

            return false;

        // Banned
        } elseif ($attempts >= $this->options['banned']) {
            return false;
        }

        // Valid
        return true;
    }

    /**
     * reset
     *
     * @param  mixed   $identifier
     * @return boolean
     */
    public function reset($identifier = null)
    {
        // No identifier
        if (!$identifier) {
            throw new \InvalidArgumentException('identifier is a required argument');
        }
        // Removed the identifier from storage
        return $this->storage->delete($identifier);
    }
}
