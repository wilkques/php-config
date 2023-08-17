<?php

namespace Wilkques\Config\Tests;

use PHPUnit\Framework\TestCase;
use Wilkques\Config\Config;

class ConfigTest extends TestCase
{
    /** @var Config */
    protected $config;

    public function setUp(): void
    {
        parent::setUp();

        $this->config = new Config;
    }

    public function configProvider()
    {
        $config = new Config;

        $config->setPath(__DIR__ . "/config");

        $config->build();

        return array(
            array($config),
        );
    }

    /**
     * @dataProvider configProvider
     * 
     * @param Config $config
     */
    public function testSetPath($config)
    {
        $this->assertEquals(
            __DIR__ . "/config",
            $config->getConfigRootPath()
        );
    }

    /**
     * @dataProvider configProvider
     * 
     * @param Config $config
     */
    public function testBuildKeyCheck($config)
    {
        $this->assertCount(4, $arrayConfig = $config->all());

        $this->assertArrayHasKey('php', $arrayConfig);

        $this->assertArrayHasKey('json', $arrayConfig);

        $this->assertArrayHasKey('yml', $arrayConfig);

        $this->assertArrayHasKey('yaml', $arrayConfig);

        $this->assertArrayHasKey('abc', $phpConfig = $config->getItem('php'));

        $this->assertArrayHasKey('hij', $phpConfig);

        $this->assertArrayHasKey('abc', $jsonConfig = $config->getItem('json'));

        $this->assertArrayHasKey('hij', $jsonConfig);

        $this->assertArrayHasKey('abc', $ymlConfig = $config->getItem('yml'));

        $this->assertArrayHasKey('hij', $ymlConfig);

        $this->assertArrayHasKey('abc', $yamlConfig = $config->getItem('yaml'));

        $this->assertArrayHasKey('hij', $yamlConfig);

        return $config;
    }

    /**
     * @dataProvider configProvider
     * 
     * @param Config $config
     */
    public function testBuild($config)
    {
        $this->assertCount(4, $config->all());

        $this->assertEquals(
            'efg',
            $config->getItem('php.abc')
        );

        $this->assertEquals(
            'opq',
            $config->getItem('php.hij.lmn')
        );

        $this->assertEquals(
            'efg',
            $config->getItem('json.abc')
        );

        $this->assertEquals(
            'opq',
            $config->getItem('json.hij.lmn')
        );

        $this->assertEquals(
            'efg',
            $config->getItem('yml.abc')
        );

        $this->assertEquals(
            'opq',
            $config->getItem('yml.hij.lmn')
        );

        $this->assertEquals(
            'efg',
            $config->getItem('yaml.abc')
        );

        $this->assertEquals(
            'opq',
            $config->getItem('yaml.hij.lmn')
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

    public function testAll()
    {
        $this->config->setItem('test', 'tests');

        $this->assertArrayHasKey('test', $this->config->all());

        $this->assertCount(1, $this->config->all());

        $this->assertArrayHasKey('test', $this->config->all());
    }

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
