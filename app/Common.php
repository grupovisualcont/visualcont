<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

use App\Controllers\Empresa;

 if (! function_exists('viewApp')) {
    /**
     * Metodo para cargar la vista junto con el menu, la empresa y tipo de cambio
     */
    function viewApp(string $page, string $name, array $data = [], array $options = [], bool $openMenu = false): string
    {
        $Empresa = new Empresa;

        $db = db_connect();

        // menu
        $sidebars = $Empresa->sidebars();

        // sub menu
        $sidebardetalles   = $Empresa->sidebardetalles();

        // empresa
        $empresa   = $Empresa->empresa();
        
        // tipo de cambio
        $tipoCambio   = $Empresa->consulta_tipo_cambio();
        
        $data = array_merge($data, [
            'page' => $page,
            'sidebars' => $sidebars,
            'sidebardetalles' => $sidebardetalles,
            'empresa' => $empresa,
            'fecha' => date('d/m/Y'),
            'tipo_cambio' => $tipoCambio,
            'openMenu' => $openMenu,
        ]);

        return view($name, $data, $options);
    }
}