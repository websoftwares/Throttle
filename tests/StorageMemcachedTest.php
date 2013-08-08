<?php
use Websoftwares\Storage\Memcached;
/**
 * Class StorageMemcacheTest
 */
class StorageMemcacheTest extends \PHPUnit_Framework_TestCase
{

    /**
     * $reflection
     * @var object
     */
    protected $reflection = null;

    public function setUp()
    {
        $this->memcached = new Memcached;
        $this->reflection = new \ReflectionClass($this->memcached);
    }

    public function testInstantiateAsObjectSucceeds()
    {
        $this->assertInstanceOf('Websoftwares\Storage\Memcached', $this->memcached);
    }

    public function testPropertyMemcachedValueSucceeds()
    {
        $this->assertInstanceOf('\\Memcached',  $this->getProperty('memcached'));
    }

    public function testSaveSucceeds()
    {
        $this->assertTrue($this->memcached->save('169.168.1.1',1,1));
    }

    public function testIncrementSucceeds()
    {
        $expected = 2;
        $this->assertEquals($expected, $this->memcached->increment('169.168.1.1'));
        $this->assertFalse($this->memcached->increment('169.168.1.2'));
    }

    public function testFileNameSucceeds()
    {
        $expected = md5('169.1.1.1');
        $this->assertEquals($expected, $this->getMethod('fileName')->invoke($this->memcached, '169.1.1.1'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSaveFails()
    {
        $this->memcached->save();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testIncrementFails()
    {
        $this->memcached->increment();
    }

    public function getProperty($property, $object = null)
    {
        $property = $this->reflection->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($object ? $object : $this->memcached);
    }

    public function getMethod($method)
    {
        $method = $this->reflection->getMethod($method);
        $method->setAccessible(true);

        return $method;
    }
}
