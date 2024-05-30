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

    /** @var static */
    static $instance;

    /**
     * @return static
     */
    public static function make()
    {
        if (static::$instance) {
            return static::$instance;
        }

        static::$instance = new static;

        return static::$instance;
    }

    /**
     * @param string $configRootPath
     * 
     * @return static
     */
    public function setConfigRootPath(string $configRootPath)
    {
        $this->configRootPath = $configRootPath;

        return $this;
    }

    /**
     * @param string $configRootPath
     * 
     * @return static
     */
    public function setPath(string $path)
    {
        return $this->setConfigRootPath($path);
    }

    /**
     * @return string
     */
    public function getConfigRootPath(): string
    {
        return $this->configRootPath;
    }

    /**
     * @param array $config
     * 
     * @return static
     */
    public function setConfig(array $config): static
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
    public function setItem(string $key, mixed $value): static
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
    public function withConfig(array $config): static
    {
        $originConfig = $this->all();

        $this->config = array_merge_distinct_recursive($originConfig, $config);

        return $this;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->config;
    }

    /**
     * @return array
     */
    public function items(): array
    {
        return $this->all();
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * 
     * @return mixed
     */
    public function getItem(string $key, mixed $default = null): mixed
    {
        return data_get($this->all(), $key, $default);
    }

    /**
     * @return static
     */
    public function build(): static
    {
        $config = [];

        foreach (dir_scan($root = $this->getConfigRootPath()) as $path) {
            $pathInfo = pathinfo($path);

            $extension = strtolower($pathInfo['extension']);

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

            // If the data is empty, then skip to the next iteration of the loop.
            if (!$data) {
                continue;
            }

            $path = str_replace($root, '', $pathInfo['dirname']);

            // If there are subfolders under the root directory, merge files from the sublayers.
            if ($path) {
                $path .= DIRECTORY_SEPARATOR . $pathInfo['filename'];

                $config = array_merge_distinct_recursive($config, $this->node($path, $data));
            } else {
                // The file name becomes the key directly.
                $config[$pathInfo['filename']] = $data;
            }
        }

        return $this->setConfig($config);
    }

    /**
     * @param string $path
     * @param array $data
     * 
     * @return array
     */
    protected function node(string $path, array $data): array
    {
        $nodeInfo = array_filter(preg_split("/\//i", $path));

        $nodeData = [];

        $current = &$nodeData;

        foreach ($nodeInfo as $node) {
            if (next($nodeInfo)) {
                $current = array(
                    $node => $current
                );
            } else {
                $current = array(
                    $node => $data
                );
            }

            $current = &$current[$node];
        }

        return $nodeData;
    }

    /**
     * @param string $path
     * 
     * @return array
     */
    public function yaml(string $path): array
    {
        if (!extension_loaded('yaml')) {
            throw new \RuntimeException("Yaml extension not loaded");
        }

        return \yaml_parse_file($path);
    }

    /**
     * @return bool
     */
    public function isEmptyConfig(): bool
    {
        return empty($this->all());
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return mixed
     */
    public function jsonSerialize(): mixed
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
    public function offsetGet(mixed $offset): mixed
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
    public function offsetSet(mixed $offset, mixed $value): void
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
    public function offsetExists(mixed $offset): bool
    {
        return !is_null($this->getItem($offset));
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        $config = $this->all();

        array_take_off_recursive($config, $offset);

        $this->setConfig($config);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->all());
    }

    /**
     * @return \Traversable
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->all());
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
