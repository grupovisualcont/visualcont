<?php

if (!function_exists('assetVersion')) {
    /**
     * Ruta para los assets
     *
     * @param string $url
     */
    function assetVersion($url)
    {
        $tempURL = str_replace("\\", "/", FCPATH) . 'assets/' . $url;
        $ver = '?v=' . @filemtime($tempURL);
        return base_url('assets/'. $url . $ver);
    }
}
