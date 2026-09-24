# Config for PHP

[![TESTS](https://github.com/wilkques/php-config/actions/workflows/github-ci.yml/badge.svg)](https://github.com/wilkques/php-config/actions/workflows/github-ci.yml)
[![Latest Stable Version](https://poser.pugx.org/wilkques/config/v/stable)](https://packagist.org/packages/wilkques/config)
[![License](https://poser.pugx.org/wilkques/config/license)](https://packagist.org/packages/wilkques/config)

[English](README.md) | 繁體中文

````
composer require wilkques/config
````

## 使用方式
1. 新增 PHP 設定檔（預設路徑 `./Config`）
    ```php
    <?php

    return [
        'abc' => 'efg',
    ];
    ```

    或用 json

    ```json
    {
        "abc": "efg",
    }
    ```

    或用 yaml<br>
    ※需要 php yaml 擴充套件

    ```yaml
    abc:
        efg: "hij"
    ```

1. 在檔案裡加入 PHP 程式碼
    ```php
    require_once 'vendor/autoload.php';

    config()
    //->setPath('<config/path>') 自訂設定路徑
    ->boot();

    $config = config('<key>'); // 取得設定項目

    $config->setItem('<key>', '<value>'); // 設定項目

    $config->withConfig([
        '<key>' => '<value>' // 用陣列設定
    ]);

    $config->getItem('<key>'); // 取得設定項目

    $config->all(); // 取得所有設定項目

    $config->foo = 'bar'; // 設定項目（透過 magic property 存取）

    $config->foo; // 取得項目（透過 magic property 存取），等同 $config->getItem('foo')
    ```

`Config::make()`（跟 `config()` 全域函式）永遠回傳**同一個共用實例**，透過 `Wilkques\Container\Container` 解析——只要呼叫過一次 `->boot()`，之後每次呼叫 `config()` 看到的都是同一個已經 boot 好的設定。
