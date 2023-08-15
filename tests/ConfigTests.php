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

        $this->assertCount(1, $config = $this->config->all());

        $this->assertArrayHasKey('settings', $config);

        $this->assertArrayHasKey('abc', $config['settings']);

        $this->assertEquals(
            'efg',
            $this->config->getItem('settings.abc')
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
