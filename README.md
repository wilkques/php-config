# Config for PHP

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

    Or yaml
    ※need php yaml extension

    ```yaml
    abc:
        efg: "hij"
    ```

1. Add PHP code in file
    ```php
    require_once 'vendor/autoload.php';

    $config = config();

    $config
    ->setPath('<config path>') // if you want change
    ->build(); // put config files

    $config->setItem('<key>', '<value>'); // set config item

    $config->getItem('<key>'); // get config item

    $config->getItems(); // get config all items
    ```