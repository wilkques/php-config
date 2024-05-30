<?php

if (!function_exists('config')) {
    /**
     * @param string $key
     * @param mixed|null $default
     * 
     * @return \App\Helpers\Config|mixed
     */
    function config(string $key = null, $default = null): mixed
    {
        $config = \Wilkques\Config\Config::make();

        if ($key) {
            return $config->getItem($key, $default);
        }

        return $config;
    }
}