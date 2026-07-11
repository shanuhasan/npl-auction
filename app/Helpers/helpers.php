<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    /**
     * Get a settings value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key, $default = null)
    {
        return Setting::get($key, $default);
    }
}
