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

    public function testDeleteSucceeds()
    {
        $identifier = 'EliteLogin';
        $this->assertTrue($this->memcached->save($identifier,1,1));
        $this->assertTrue($this->memcached->delete($identifier));
        $this->assertFalse($this->memcached->delete($identifier));
    }

    public function testGetSucceeds()
    {
        $identifier = 'EliteLogin';
        $expected = 1;
        $this->assertTrue($this->memcached->save($identifier,1,1));
        $actual = $this->memcached->get($identifier);
        $this->assertEquals($expected , $actual);
        $this->assertInternalType('int', $actual);
        $noResult = 'NoResult';
        $this->assertInternalType('boolean', $this->memcached->get($noResult));
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
    public function testGetFails()
    {
        $this->memcached->get();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testDeleteFails()
    {
        $this->memcached->delete();
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
