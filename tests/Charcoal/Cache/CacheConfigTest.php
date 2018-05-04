<?php

namespace Charcoal\Tests\Cache;

use InvalidArgumentException;

// From 'charcoal-cache'
use Charcoal\Tests\AbstractTestCase;
use Charcoal\Cache\CacheConfig;

/**
 * Test CacheConfig
 *
 * @coversDefaultClass \Charcoal\Cache\CacheConfig
 */
class CacheConfigTest extends AbstractTestCase
{
    /**
     * @var CacheConfig
     */
    public $cfg;

    /**
     * Create the CacheConfig instance.
     */
    public function setUp()
    {
        $this->cfg = $this->configFactory();
    }

    /**
     * Create a new CacheConfig instance.
     *
     * @param  array $args Parameters for the initialization of a CacheConfig.
     * @return CacheConfig
     */
    public function configFactory(array $args = [])
    {
        return new CacheConfig($args);
    }

    /**
     * @covers ::defaults
     * @covers ::active
     * @covers ::types
     * @covers ::defaultTtl
     * @covers ::prefix
     */
    public function testDefaults()
    {
        $this->assertEquals('charcoal', CacheConfig::DEFAULT_NAMESPACE);
        $this->assertEquals((60 * 60), CacheConfig::HOUR_IN_SECONDS);
        $this->assertEquals((60 * 60 * 24), CacheConfig::DAY_IN_SECONDS);
        $this->assertEquals((60 * 60 * 24 * 7), CacheConfig::WEEK_IN_SECONDS);

        $defaults = $this->cfg->defaults();

        $this->assertArrayHasKey('active', $defaults);
        $this->assertEquals($defaults['active'], $this->cfg->active());

        $this->assertArrayHasKey('types', $defaults);
        $this->assertEquals($defaults['types'], $this->cfg->types());

        $this->assertArrayHasKey('default_ttl', $defaults);
        $this->assertEquals($defaults['default_ttl'], $this->cfg->defaultTtl());

        $this->assertArrayHasKey('prefix', $defaults);
        $this->assertEquals($defaults['prefix'], $this->cfg->prefix());
    }

    /**
     * @covers ::setActive
     * @covers ::active
     */
    public function testActive()
    {
        // Chainable
        $that = $this->cfg->setActive(false);
        $this->assertSame($this->cfg, $that);

        // Mutated State
        $this->assertFalse($this->cfg->active());
    }

    /**
     * @covers ::setTypes
     * @covers ::types
     */
    public function testReplaceDrivers()
    {
        // Chainable
        $that = $this->cfg->setTypes([ 'memcache', 'noop' ]);
        $this->assertSame($this->cfg, $that);

        // Mutated State
        $types = $this->cfg->types();
        $this->assertNotContains('memory', $types);
        $this->assertContains('memcache', $types);
        $this->assertContains('noop', $types);
    }

    /**
     * @covers ::addTypes
     * @covers ::addType
     * @covers ::types
     */
    public function testAddDrivers()
    {
        // Chainable
        $that = $this->cfg->addTypes([ 'memcache', 'noop' ]);
        $this->assertSame($this->cfg, $that);

        // Mutated State
        $types = $this->cfg->types();
        $this->assertContains('memory', $types);
        $this->assertContains('memcache', $types);
        $this->assertContains('noop', $types);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Invalid cache type: "foobar"
     *
     * @covers ::validTypes
     * @covers ::addType
     */
    public function testAddDriverOnInvalidType()
    {
        $this->cfg->addType('foobar');
    }

    /**
     * @covers ::setDefaultTtl
     * @covers ::defaultTtl
     */
    public function testDefaultTtl()
    {
        // Chainable
        $that = $this->cfg->setDefaultTtl(42);
        $this->assertSame($this->cfg, $that);

        // Mutated State
        $this->assertEquals(42, $this->cfg->defaultTtl());
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage TTL must be an integer (seconds)
     *
     * @covers ::setDefaultTtl
     */
    public function testSetDefaultTtlOnInvalidType()
    {
        $this->cfg->setDefaultTtl('foo');
    }

    /**
     * @covers ::setPrefix
     * @covers ::prefix
     */
    public function testPrefix()
    {
        // Chainable
        $that = $this->cfg->setPrefix('foo');
        $this->assertSame($this->cfg, $that);

        // Mutated State
        $this->assertEquals('foo', $this->cfg->prefix());
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Prefix must be a string
     *
     * @covers ::setPrefix
     */
    public function testSetPrefixOnInvalidType()
    {
        $this->cfg->setPrefix(false);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Prefix must be alphanumeric
     *
     * @covers ::setPrefix
     */
    public function testSetPrefixOnInvalidValue()
    {
        $this->cfg->setPrefix('foo!#$bar');
    }
}
