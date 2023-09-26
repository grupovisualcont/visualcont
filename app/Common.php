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

 if (! function_exists('viewApp')) {
    /**
     * Metodo para cargar la vista junto con el menu, la empresa y tipo de cambio
     */
    function viewApp(string $page, string $name, array $data = [], array $options = []): string
    {
        $db = db_connect();
        // menu
        $fecha = date('Y-m-d');
        $codEmpresa = $_COOKIE['empresa'];
        $builder = $db->table('sidebar');
        $sidebars   = $builder->get()->getResult('array');
        // sub menu
        $builder = $db->table('sidebardetalles');
        $sidebardetalles   = $builder->get()->getResult('array');
        // empresa
        $builder = $db->table('empresas');
        $empresa   = $builder->where('codEmpresa', $codEmpresa)->get()->getRow();
        // tipo de cambio
        $builder = $db->table('tipocambio');
        $tipoCambio   = $builder->where('CodEmpresa', $codEmpresa)
                    ->where('FechaTipoCambio', $fecha . ' 00:00:00')->get()->getRow();
        
        $data = array_merge($data, [
            'page' => $page,
            'sidebars' => $sidebars,
            'sidebardetalles' => $sidebardetalles,
            'razon_social' => $empresa->RazonSocial,
            'ruc' => $empresa->Ruc,
            'fecha' => date('d/m/Y'),
            'tipo_cambio' => $tipoCambio,
        ]);
        return view($name, $data, $options);
    }
}