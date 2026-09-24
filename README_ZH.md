# Config for PHP

[![TESTS](https://github.com/wilkques/php-config/actions/workflows/github-ci.yml/badge.svg)](https://github.com/wilkques/php-config/actions/workflows/github-ci.yml)
[![Latest Stable Version](https://poser.pugx.org/wilkques/config/v/stable)](https://packagist.org/packages/wilkques/config)
[![License](https://poser.pugx.org/wilkques/config/license)](https://packagist.org/packages/wilkques/config)

[English](README.md) | 繁體中文

從一個目錄樹裡讀取 PHP／JSON／YAML 設定檔，整合成一個可用點記號（dot notation）存取的設定物件，並保持與 **PHP 5.3** 相容。

## 需求

- PHP >= 5.3（已在 5.3、5.6、7.0、7.1、7.2、7.3、7.4、8.0、8.1、8.2、8.3 測試過）
- `ext-yaml`——只有在使用 `.yaml`/`.yml` 設定檔時才需要

## 安裝

```
composer require wilkques/config
```

## 使用方式

1. 新增設定檔（預設路徑 `./config`，可以任意巢狀——子目錄會變成巢狀 key）
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

### 點記號（dot notation）與巢狀設定檔

設定檔的檔名會變成最上層的 key，子目錄也是同樣的規則——例如 `config/database/mysql.php` 會被讀成 `database.mysql`。`getItem()`/`setItem()` 都接受用點分隔的 key 深入巢狀結構：

```php
$config->getItem('database.mysql.host'); // 深入 config/database/mysql.php 的 'host' key
$config->setItem('database.mysql.host', '127.0.0.1');
```

## API 參考

以下每個方法都附上實際可執行的範例（來自測試套件 `tests/Units/Higher/ConfigTest.php` / `tests/Units/Lower/ConfigTest.php`，已在 PHP 5.3.10、7.4、8.3 上驗證過）。

| 方法 | 說明 | 範例 |
| --- | --- | --- |
| `make()` *（靜態方法）* | 透過 container 解析出共用的 `Config` 實例。 | `Config::make();` |
| `setConfigRootPath($configRootPath)` / `setPath($path)` | 設定 `boot()` 要掃描的設定目錄（預設 `./config`）。 | `$config->setPath('/app/config');` |
| `getConfigRootPath()` | 取得目前設定的設定根目錄。 | `$config->getConfigRootPath(); // '/app/config'` |
| `boot()` | 遞迴掃描設定根目錄，載入所有 `.php`/`.json`/`.yaml`/`.yml` 檔案。 | `$config->boot();` |
| `all()` / `items()` | 取得所有已載入的設定項目。 | `$config->all(); // ['database' => [...], ...]` |
| `getItem($key, $default = null)` | 用點記號 key 取得設定值。 | `$config->getItem('database.mysql.host'); // '127.0.0.1'` |
| `setItem($key, $value)` | 用點記號 key 設定設定值。 | `$config->setItem('database.mysql.host', '127.0.0.1');` |
| `setConfig($config)` | 取代整個已載入的設定陣列。 | `$config->setConfig(['abc' => 'efg']);` |
| `withConfig($config)` | 把一個陣列遞迴合併進現有設定。 | `$config->withConfig(['abc' => 'efg']);` |
| `isEmptyConfig()` / `isNotEmptyConfig()` | 判斷目前是否已載入／設定過任何設定。 | `$config->isEmptyConfig(); // boot() 之前是 true` |
| `yaml($path)` | 直接解析一個 YAML 檔（沒裝 `ext-yaml` 會丟例外）。 | `$config->yaml('/app/config/app.yaml');` |

### Magic property、陣列、迭代器存取

`Config` 也實作了 `ArrayAccess`、`Countable`、`IteratorAggregate`、`JsonSerializable`，還有 `__get`/`__set`——這些都是 `getItem()`/`setItem()`/`all()` 的等價捷徑。

| 存取方式 | 範例 |
| --- | --- |
| Magic property | `$config->foo = 'bar'; $config->foo; // 'bar'` |
| 陣列存取 | `$config['foo'] = 'bar'; $config['foo']; // 'bar'` |
| `count($config)` | 計算最上層設定項目的數量。 |
| `foreach ($config as $key => $value)` | 迭代最上層設定項目。 |
| `json_encode($config)` | 序列化 `all()`。 |

## 測試

```
composer install
vendor/bin/phpunit -c phpunit-higher.xml   # PHP 7+
vendor/bin/phpunit -c phpunit-lower.xml    # PHP 5.3
```

## 授權

MIT
