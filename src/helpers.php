<?php

if (!function_exists('config')) {
    /**
     * @param string $key
     * @param mixed|null $default
     * 
     * @return \Wilkques\Config\Config|mixed
     */
    function config($key = null, $default = null)
    {
        $config = \Wilkques\Config\Config::make();

        if ($key) {
            return $config->getItem($key, $default);
        }

        return $config;
    }
}