<?php

namespace Wilkques\Config;

class Config implements \JsonSerializable, \ArrayAccess, \Countable, \IteratorAggregate
{
    /** @var array */
    protected $config = [];

    /**
     * Config path default ./Config
     * 
     * @var string
     */
    protected $configRootPath = './Config';

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
     * @param string $key
     * @param mixed $value
     * 
     * @return static
     */
    public function setItem($key, $value)
    {
        $originConfig = $this->all();
        
        $this->config = data_set($originConfig, $key, $value);

        return $this;
    }

    /**
     * @param array $config
     * 
     * @return static
     */
    public function withConfig(array $config)
    {
        $originConfig = $this->all();
        
        $this->config = array_merge_recursive_distinct($originConfig, $config);

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
        return data_get($this->all(), $key, $default);
    }

    /**
     * @return static
     */
    public function build()
    {
        $config = array();

        foreach (dir_scan($this->getConfigRootPath()) as $path) {
            if (preg_match('/php/i', $path)) {
                preg_match('/(\w+).php/i', $path, $matches);

                $config[array_pop($matches)] = require_once $path;
            } else if (preg_match('/json/i', $path)) {
                preg_match('/(\w+).json/i', $path, $matches);

                $jsonString = file_get_contents($path);

                $config[array_pop($matches)] = json_decode($jsonString, true);
            } else if (preg_match('/yaml/i', $path)) {
                preg_match('/(\w+).yaml/i', $path, $matches);

                $config[array_pop($matches)] = $this->yaml($path);
            } else if (preg_match('/yml/i', $path)) {
                preg_match('/(\w+).yml/i', $path, $matches);

                $config[array_pop($matches)] = $this->yaml($path);
            }
        }

        return $this->setConfig($config);
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
        return empty($this->all());
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
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
    public function offsetSet($offset, $value)
    {
        return $this->setItem($offset, $value);
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param  mixed  $offset
     * 
     * @return bool
     */
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
    public function offsetUnset($offset)
    {
        $config = $this->all();
        
        array_take_off_recursive($config, $offset);

        $this->setConfig($config);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->all());
    }

    /**
     * @return mixed
     */
    public function getIterator()
    {
        yield from $this->all();
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->setItem($key, $value);
    }

    /**
     * @param string|null $key
     * 
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getItem($key);
    }
}
