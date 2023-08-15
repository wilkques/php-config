<?php

namespace Wilkques\Config\Tests;

use PHPUnit\Framework\TestCase;
use Wilkques\Config\Config;

class ConfigTests extends TestCase
{
    /** @var Config */
    protected $config;

    public function setUp(): void
    {
        parent::setUp();

        $this->config = new Config;
    }

    public function testSetPath()
    {
        $this->config->setPath(__DIR__ . "/config");

        $this->assertEquals(
            __DIR__ . "/config",
            $this->config->getConfigRootPath()
        );
    }

    public function testBuild()
    {
        $this->config->setPath(__DIR__ . "/config");

        $this->config->build();

        $this->assertCount(4, $config = $this->config->all());

        $this->assertArrayHasKey('php', $config);

        $this->assertArrayHasKey('json', $config);

        $this->assertArrayHasKey('yml', $config);

        $this->assertArrayHasKey('yaml', $config);

        $this->assertArrayHasKey('abc', $config['php']);

        $this->assertArrayHasKey('abc', $config['json']);

        $this->assertArrayHasKey('abc', $config['yml']);

        $this->assertArrayHasKey('abc', $config['yaml']);

        $this->assertEquals(
            'efg',
            $this->config->getItem('php.abc')
        );

        $this->assertEquals(
            'efg',
            $this->config->getItem('json.abc')
        );

        $this->assertEquals(
            'efg',
            $this->config->getItem('yml.abc')
        );

        $this->assertEquals(
            'efg',
            $this->config->getItem('yaml.abc')
        );
    }

    public function testSetItem()
    {
        $this->config->setItem('test', 'tests');

        $this->assertArrayHasKey('test', $this->config->all());

        $this->assertEquals(
            'tests',
            $this->config->getItem('test')
        );
    }

    /**
     * @depends testSetItem
     */
    public function testAll()
    {
        $this->config->setItem('test', 'tests');

        $this->assertArrayHasKey('test', $this->config->all());

        $this->assertCount(1, $this->config->all());

        $this->assertArrayHasKey('test', $this->config->all());
    }

    /**
     * @depends testSetItem
     */
    public function testWithConfig()
    {
        $this->config->withConfig([
            'test' => 'testss'
        ]);

        $this->assertCount(1, $this->config->all());

        $this->assertArrayHasKey('test', $this->config->all());

        $this->assertEquals(
            'testss',
            $this->config->getItem('test')
        );
    }
}
