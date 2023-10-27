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

if (!function_exists('baseUrlWeb')) {
    /**
     * devuelve la ruta deifina para el uso del cliente
     * 
     * @param string $url
     * @return string
     */
    function baseUrlWeb(string $url): string
    {
        return base_url('web/' . $url);
    }
}