<?php

function wealf_array_element($key, $array, $default = null)
{
    return isset($array[$key]) ? $array[$key] : $default;
}

function wealf_get_file($file = '', $type = 'url')
{
    $dir             = dirname(__DIR__, 1);
    $theme_root      = get_template_directory();
    $wealf_directory = trailingslashit(str_replace($theme_root, '', $dir));

    if (strpos($dir, $theme_root) !== false) {
        $file_path = $file ? $wealf_directory . $file : $wealf_directory;

        if ($type == 'url') {
            return get_theme_file_uri($file_path);
        } else {
            return get_theme_file_path($file_path);
        }
    } else {
        if ($type == 'url') {
            if ($file) {
                return plugin_dir_url(__FILE__) . $file;
            } else {
                return plugin_dir_url(__FILE__);
            }
        } else {
            if ($file) {
                return plugin_dir_path(__FILE__) . $file;
            } else {
                return plugin_dir_path(__FILE__);
            }
        }
    }
}
