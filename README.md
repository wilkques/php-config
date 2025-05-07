# Config for PHP

[![TESTS](https://github.com/wilkques/php-config/actions/workflows/github-ci.yml/badge.svg)](https://github.com/wilkques/php-config/actions/workflows/github-ci.yml)
[![Latest Stable Version](https://poser.pugx.org/wilkques/config/v/stable)](https://packagist.org/packages/wilkques/config)
[![License](https://poser.pugx.org/wilkques/config/license)](https://packagist.org/packages/wilkques/config)

````
composer require wilkques/config
````

## How to use
1. Add PHP config file (path default `./Config`)
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
    ```