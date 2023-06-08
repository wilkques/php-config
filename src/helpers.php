<?php

if (!function_exists('config')) {
    /**
     * @param string $key
     * 
     * @return mixed
     */
    function config()
    {
        return container(\Wilkques\Config\Config::class);
    }
}