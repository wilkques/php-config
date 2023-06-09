<?php

if (!function_exists('config')) {
    /**
     * @param string $key
     * 
     * @return mixed
     */
    function config($key = null)
    {
        /** @var \Wilkques\Config\Config */
        $config = container(\Wilkques\Config\Config::class);

        if ($key) {
            if ($config->isEmptyConfig()) {
                $config->build();
            }

            return $config->getItem($key);
        }

        return $config;
    }
}
