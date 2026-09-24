# Config for PHP

[![TESTS](https://github.com/wilkques/php-config/actions/workflows/github-ci.yml/badge.svg)](https://github.com/wilkques/php-config/actions/workflows/github-ci.yml)
[![Latest Stable Version](https://poser.pugx.org/wilkques/config/v/stable)](https://packagist.org/packages/wilkques/config)
[![License](https://poser.pugx.org/wilkques/config/license)](https://packagist.org/packages/wilkques/config)

English | [繁體中文](README_ZH.md)

Load PHP/JSON/YAML config files from a directory tree into a single, dot-notation-accessible config store, kept compatible all the way back to **PHP 5.3**.

## Requirements

- PHP >= 5.3 (tested against 5.3, 5.6, 7.0, 7.1, 7.2, 7.3, 7.4, 8.0, 8.1, 8.2, 8.3)
- `ext-yaml` — only required if you use `.yaml`/`.yml` config files

## Installation

```
composer require wilkques/config
```

## Usage

1. Add a config file (path default `./config`, any nesting depth — subdirectories become nested keys)
    ```php
    <?php

    return [
        'abc' => 'efg',
    ];
    ```

    Or json

    ```json
    {
        "abc": "efg",
    }
    ```

    Or yaml<br>
    ※need php yaml extension

    ```yaml
    abc:
        efg: "hij"
    ```

1. Add PHP code in file
    ```php
    require_once 'vendor/autoload.php';

    config()
    //->setPath('<config/path>') custom config path
    ->boot();

    $config = config('<key>'); // get config item

    $config->setItem('<key>', '<value>'); // set config item

    $config->withConfig([
        '<key>' => '<value>' // set config with array
    ]);

    $config->getItem('<key>'); // get config item

    $config->all(); // get config all items

    $config->foo = 'bar'; // set config item (magic property access)

    $config->foo; // get config item (magic property access), same as $config->getItem('foo')
    ```

`Config::make()` (and the `config()` global helper) always return the **same shared instance**, resolved through `Wilkques\Container\Container` — calling `->boot()` once is enough, every later `config()` call sees the same booted config.

### Dot notation & nested files

A config file's name becomes its top-level key, and subdirectories nest the same way — a file at `config/database/mysql.php` is read as `database.mysql`. `getItem()`/`setItem()` accept dot-separated keys to reach into that nesting:

```php
$config->getItem('database.mysql.host'); // reaches into config/database/mysql.php's 'host' key
$config->setItem('database.mysql.host', '127.0.0.1');
```

## API Reference

Every method below has a runnable example, taken from the test suite (`tests/Units/Higher/ConfigTest.php` / `tests/Units/Lower/ConfigTest.php`) and verified on PHP 5.3.10, 7.4, and 8.3.

| Method | Description | Example |
| --- | --- | --- |
| `make()` *(static)* | Resolve the shared `Config` instance via the container. | `Config::make();` |
| `setConfigRootPath($configRootPath)` / `setPath($path)` | Set the directory `boot()` scans for config files (default `./config`). | `$config->setPath('/app/config');` |
| `getConfigRootPath()` | Get the currently-configured config root directory. | `$config->getConfigRootPath(); // '/app/config'` |
| `boot()` | Scan the config root (recursively) and load every `.php`/`.json`/`.yaml`/`.yml` file found. | `$config->boot();` |
| `all()` / `items()` | Get every loaded config item. | `$config->all(); // ['database' => [...], ...]` |
| `getItem($key, $default = null)` | Get a config value by dot-notation key. | `$config->getItem('database.mysql.host'); // '127.0.0.1'` |
| `setItem($key, $value)` | Set a config value by dot-notation key. | `$config->setItem('database.mysql.host', '127.0.0.1');` |
| `setConfig($config)` | Replace the entire loaded config array. | `$config->setConfig(['abc' => 'efg']);` |
| `withConfig($config)` | Recursively merge an array into the existing config. | `$config->withConfig(['abc' => 'efg']);` |
| `isEmptyConfig()` / `isNotEmptyConfig()` | Determine whether any config has been loaded/set. | `$config->isEmptyConfig(); // true before boot()` |
| `yaml($path)` | Parse a YAML file directly (throws if `ext-yaml` isn't loaded). | `$config->yaml('/app/config/app.yaml');` |

### Magic property, array, and iterator access

`Config` also implements `ArrayAccess`, `Countable`, `IteratorAggregate`, and `JsonSerializable`, plus `__get`/`__set` — all equivalent shortcuts for `getItem()`/`setItem()`/`all()`.

| Access style | Example |
| --- | --- |
| Magic property | `$config->foo = 'bar'; $config->foo; // 'bar'` |
| Array access | `$config['foo'] = 'bar'; $config['foo']; // 'bar'` |
| `count($config)` | Counts the top-level config items. |
| `foreach ($config as $key => $value)` | Iterates the top-level config items. |
| `json_encode($config)` | Serializes `all()`. |

## Testing

```
composer install
vendor/bin/phpunit -c phpunit-higher.xml   # PHP 7+
vendor/bin/phpunit -c phpunit-lower.xml    # PHP 5.3
```

## License

MIT
