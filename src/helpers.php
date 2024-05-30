<?php

if (!function_exists('config')) {
    /**
     * @param string|int $key
     * @param mixed|null $default
     * 
     * @return \App\Helpers\Config|mixed
     */
    function config(string|int $key = null, mixed $default = null): mixed
    {
        $config = \Wilkques\Config\Config::make();

        if ($key) {
            return $config->getItem($key, $default);
        }

        return $config;
    }
}