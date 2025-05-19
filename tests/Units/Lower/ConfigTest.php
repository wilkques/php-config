<?php

namespace Wilkques\Config\Tests\Units\Php\Lower;

use PHPUnit\Framework\TestCase;
use Wilkques\Config\Config;

class ConfigTest extends TestCase
{
    /** @var Config */
    protected $config;

    public function setUp()
    {
        parent::setUp();

        $this->config = config();
    }

    public static function configProvider()
    {
        $config = config();

        $config->setPath(dirname(dirname(__DIR__)) . "/config");

        $config->boot();

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
            dirname(dirname(__DIR__)) . "/config",
            $config->getConfigRootPath()
        );
    }

    /**
     * @dataProvider configProvider
     * 
     * @param Config $config
     */
    public function testBuildRootKeyCheck($config)
    {
        $this->assertCount(5, $arrayConfig = $config->all());

        $this->assertArrayHasKey('php', $arrayConfig);

        $this->assertArrayHasKey('json', $arrayConfig);

        $this->assertArrayHasKey('yml', $arrayConfig);

        $this->assertArrayHasKey('yaml', $arrayConfig);

        $this->assertArrayHasKey('folders', $arrayConfig);

        $this->assertArrayHasKey('abc', $phpConfig = $config->getItem('php'));

        $this->assertArrayHasKey('hij', $phpConfig);

        $this->assertArrayHasKey('abc', $jsonConfig = $config->getItem('json'));

        $this->assertArrayHasKey('hij', $jsonConfig);

        $this->assertArrayHasKey('abc', $ymlConfig = $config->getItem('yml'));

        $this->assertArrayHasKey('hij', $ymlConfig);

        $this->assertArrayHasKey('abc', $yamlConfig = $config->getItem('yaml'));

        $this->assertArrayHasKey('hij', $yamlConfig);
    }

    /**
     * @dataProvider configProvider
     * 
     * @param Config $config
     */
    public function testBuildSubFoldersKeyCheck($config)
    {
        $this->assertCount(5, $config->all());

        $this->assertArrayHasKey('php', $folderConfig = $config->getItem('folders'));

        $this->assertArrayHasKey('json', $folderConfig);

        $this->assertArrayHasKey('yml', $folderConfig);

        $this->assertArrayHasKey('yaml', $folderConfig);

        $this->assertArrayHasKey('abc', $phpConfig = $config->getItem('folders.php'));

        $this->assertArrayHasKey('hij', $phpConfig);

        $this->assertArrayHasKey('abc', $jsonConfig = $config->getItem('folders.json'));

        $this->assertArrayHasKey('hij', $jsonConfig);

        $this->assertArrayHasKey('abc', $ymlConfig = $config->getItem('folders.yml'));

        $this->assertArrayHasKey('hij', $ymlConfig);

        $this->assertArrayHasKey('abc', $yamlConfig = $config->getItem('folders.yaml'));

        $this->assertArrayHasKey('hij', $yamlConfig);
    }

    /**
     * @dataProvider configProvider
     * 
     * @param Config $config
     */
    public function testBuildRoot($config)
    {
        $this->assertCount(5, $config->all());

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

    /**
     * @dataProvider configProvider
     * 
     * @param Config $config
     */
    public function testBuildSubFolders($config)
    {
        $this->assertCount(5, $config->all());

        $this->assertEquals(
            'efg',
            $config->getItem('folders.php.abc')
        );

        $this->assertEquals(
            'opq',
            $config->getItem('folders.php.hij.lmn')
        );

        $this->assertEquals(
            'efg',
            $config->getItem('folders.json.abc')
        );

        $this->assertEquals(
            'opq',
            $config->getItem('folders.json.hij.lmn')
        );

        $this->assertEquals(
            'efg',
            $config->getItem('folders.yml.abc')
        );

        $this->assertEquals(
            'opq',
            $config->getItem('folders.yml.hij.lmn')
        );

        $this->assertEquals(
            'efg',
            $config->getItem('folders.yaml.abc')
        );

        $this->assertEquals(
            'opq',
            $config->getItem('folders.yaml.hij.lmn')
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
