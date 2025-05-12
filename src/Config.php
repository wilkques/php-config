<?php

namespace Wilkques\Config;

use Wilkques\Helpers\Arrays;
use Wilkques\Helpers\Objects;

class Config implements \JsonSerializable, \ArrayAccess, \Countable, \IteratorAggregate
{
    /** @var array */
    protected $config = array();

    /**
     * Config path default ./Config
     * 
     * @var string
     */
    protected $configRootPath = './config';

    /**
     * @return static
     */
    public static function make()
    {
        return (new \Wilkques\Container\Container)->make('\Wilkques\Config\Config');
    }

    /**
     * @param string $configRootPath
     * 
     * @return static
     */
    public function setConfigRootPath($configRootPath)
    {
        $this->configRootPath = $configRootPath;

        return $this;
    }

    /**
     * @param string $configRootPath
     * 
     * @return static
     */
    public function setPath($path)
    {
        return $this->setConfigRootPath($path);
    }

    /**
     * @return string
     */
    public function getConfigRootPath()
    {
        return $this->configRootPath;
    }

    /**
     * @param array $config
     * 
     * @return static
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @param string|int $key
     * @param mixed $value
     * 
     * @return static
     */
    public function setItem($key, $value)
    {
        $originConfig = $this->all();

        $this->config = Objects::set($originConfig, $key, $value);

        return $this;
    }

    /**
     * @param array $config
     * 
     * @return static
     */
    public function withConfig($config)
    {
        $originConfig = $this->all();

        $this->config = Arrays::mergeDistinctRecursive($originConfig, $config);

        return $this;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->config;
    }

    /**
     * @return array
     */
    public function items()
    {
        return $this->all();
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * 
     * @return mixed
     */
    public function getItem($key, $default = null)
    {
        return Objects::get($this->all(), $key, $default);
    }

    /**
     * @return static
     */
    public function boot()
    {
        $filesystem = new \Wilkques\Filesystem\Filesystem;

        $dirs = $filesystem->directories($this->getConfigRootPath());

        return $this->setConfig(
            $this->searchConfig($dirs, $filesystem)
        );
    }

    /**
     * @param array $dirs
     * @param \Wilkques\Filesystem\Filesystem $filesystem
     * 
     * @return array
     */
    public function searchConfig($dirs, $filesystem)
    {
        $config = array();

        foreach ($dirs as $dir) {
            $splFile = new \SplFileInfo($dir);

            if ($splFile->isDir()) {
                $dir = $splFile->getPathname();

                $config[basename($dir)] = $this->searchConfig(
                    $filesystem->searchInDirectory($dir),
                    $filesystem
                );

                continue;
            }

            $extension = $splFile->getExtension();

            $key = $splFile->getBasename(".{$extension}");

            $config[$key] = $this->format($extension, $dir);
        }

        return Arrays::filter($config);
    }

    /**
     * @param string $extension
     * @param string $path
     * 
     * @return array
     */
    protected function format($extension, $path)
    {
        $extension = strtolower($extension);

        switch ($extension) {
            case 'php':
                $data = require $path;
                break;
            case 'json':
                $jsonString = file_get_contents($path);

                $data = json_decode($jsonString, true);
                break;
            case 'yaml':
            case 'yml':
                $data = $this->yaml($path);
                break;
            default:
                $data = null;
                break;
        }

        return $data;
    }

    /**
     * @param string $path
     * 
     * @return array
     */
    public function yaml($path)
    {
        if (!extension_loaded('yaml')) {
            throw new \RuntimeException("Yaml extension not loaded");
        }

        return \yaml_parse_file($path);
    }

    /**
     * @return bool
     */
    public function isEmptyConfig()
    {
        return empty($this->config);
    }

    /**
     * @return bool
     */
    public function isNotEmptyConfig()
    {
        return ! $this->isEmptyConfig();
    }

    /**
     * @return int
     */
    #[\ReturnTypeWillChange]
    public function count()
    {
        return iterator_count($this->all());
    }

    /**
     * @return \Traversable
     */
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        return new \ArrayIterator($this->all());
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->all();
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * 
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->getItem($offset);
    }

    /**
     * Set the value for a given offset.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     * 
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        $this->setItem($offset, $value);
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param  mixed  $offset
     * 
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return !is_null($this->getItem($offset));
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        $config = $this->all();

        array_take_off_recursive($config, $offset);

        $this->setConfig($config);
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function __set(string $key, mixed $value)
    {
        $this->setItem($key, $value);
    }

    /**
     * @param string $key
     * 
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->getItem($key);
    }
}
