<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('app/panel/index', 'Panel::index');

$routes->post('login', 'Empresa::login');

$routes->group('app', static function($routes) {

    $routes->group('mantenimiento', static function($routes) {

        $routes->group('socio_negocio', static function($routes) {

            $routes->get('index', 'Mantenimiento::socio_negocio');
            $routes->get('create', 'Mantenimiento::socio_negocio_nuevo');
            $routes->post('save', 'Mantenimiento::socio_negocio_grabar');
            $routes->get('edit/(:any)', 'Mantenimiento::socio_negocio_editar/$1');
            $routes->post('update', 'Mantenimiento::socio_negocio_actualizar');
            $routes->get('delete/(:any)', 'Mantenimiento::socio_negocio_eliminar/$1'); 
            $routes->get('excel', 'Mantenimiento::socio_negocio_reporte_excel');
            $routes->get('pdf', 'Mantenimiento::socio_negocio_reporte_pdf');
            $routes->post('consulta_duplicados', 'Mantenimiento::socio_negocio_consulta_duplicados');

        });
    });

    $routes->group('mantenience', static function($routes) {
        
        $routes->group('business_partner', static function($routes) {
        
            $routes->get('autocompletado', 'SocioNegocios::autoCompletado');
        });
    });

    $routes->group('purchase', static function($routes) {

        $routes->get('create', 'Compras::crear');
    });

    $routes->group('type_vouchers', static function($routes) {
        
        $routes->get('autocompletado', 'TipoVouchers::autoCompletado');
    });
});