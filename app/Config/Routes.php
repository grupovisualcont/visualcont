<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('app/panel/index', 'Panel::index');

$routes->post('login', 'Empresa::login');
$routes->post('empresa/consulta_sunat', 'Empresa::consulta_sunat');
$routes->post('empresa/consulta_tipo_cambio', 'Empresa::consulta_tipo_cambio');

$routes->group('app', static function($routes) {

    $routes->post('type_person/autocompletado', 'TipoPersona::autocompletado');
    $routes->group('identity_document_type', static function($routes) {
        $routes->post('document/autocompletado', 'TipoDocumentoIdentidad::autocompletado');
        $routes->post('bank/autocompletado', 'TipoDocumentoIdentidad::autocompletado_banco');
    });
    
    $routes->post('attached/autocompletado/(:any)/(:any)/(:any)', 'Anexos::autocompletado/$1/$2/$3');
    $routes->post('ts27Vinculo/autocompletado', 'Ts27Vinculos::autocompletado');

    $routes->group('mantenience', static function($routes) {
        
        $routes->group('business_partner', static function($routes) {
        
            $routes->get('index', 'SocioNegocios::index');
            $routes->get('create', 'SocioNegocios::create');
            $routes->post('save', 'SocioNegocios::save');
            $routes->get('edit/(:any)', 'SocioNegocios::edit/$1');
            $routes->post('update', 'SocioNegocios::update');
            $routes->get('delete/(:any)', 'SocioNegocios::delete/$1'); 
            $routes->get('excel', 'SocioNegocios::excel');
            $routes->get('pdf', 'SocioNegocios::pdf');
            $routes->post('consulta_duplicados', 'SocioNegocios::consulta_duplicados');
            $routes->get('autocompletado', 'SocioNegocios::autoCompletado');

        });

        $routes->group('fixed_assets', static function($routes) {
        
            $routes->get('index', 'ActivosFijos::index');
            $routes->get('create', 'ActivosFijos::create');
            $routes->post('save', 'ActivosFijos::save');
            $routes->get('edit/(:any)', 'ActivosFijos::edit/$1');
            $routes->post('update', 'ActivosFijos::update');
            $routes->get('delete/(:any)', 'ActivosFijos::delete/$1'); 
            $routes->get('excel', 'ActivosFijos::excel');
            $routes->get('pdf', 'ActivosFijos::pdf');
            $routes->post('consulta_nombre', 'ActivosFijos::consulta_nombre');

        });

        $routes->group('asset_types', static function($routes) {
        
            $routes->get('index', 'TiposActivos::index');
            $routes->get('create', 'TiposActivos::create');
            $routes->post('save', 'TiposActivos::save');
            $routes->get('edit/(:any)', 'TiposActivos::edit/$1');
            $routes->post('update', 'TiposActivos::update');
            $routes->get('delete/(:any)', 'TiposActivos::delete/$1'); 
            $routes->get('excel', 'TiposActivos::excel');
            $routes->get('pdf', 'TiposActivos::pdf');
            $routes->post('consulta_nombre', 'TiposActivos::consulta_nombre');

        });

        $routes->group('accounting_plan', static function($routes) {
        
            $routes->get('index', 'PlanContable::index');
            $routes->get('create', 'PlanContable::create');
            $routes->post('save', 'PlanContable::save');
            $routes->get('edit/(:any)', 'PlanContable::edit/$1');
            $routes->post('update', 'PlanContable::update');
            $routes->get('delete/(:any)', 'PlanContable::delete/$1'); 
            $routes->get('excel', 'PlanContable::excel');
            $routes->get('pdf', 'PlanContable::pdf');
            $routes->post('datos', 'PlanContable::datos');
            $routes->post('consulta_cuenta', 'PlanContable::consulta_cuenta');
            $routes->post('autocompletado', 'PlanContable::autocompletado');

        });

        $routes->group('types_of_vouchers', static function($routes) {
        
            $routes->get('index', 'TipoVouchers::index');
            $routes->get('create', 'TipoVouchers::create');
            $routes->post('save', 'TipoVouchers::save');
            $routes->get('edit/(:any)', 'TipoVouchers::edit/$1');
            $routes->post('update', 'TipoVouchers::update');
            $routes->get('delete/(:any)', 'TipoVouchers::delete/$1'); 
            $routes->get('excel', 'TipoVouchers::excel');
            $routes->get('pdf', 'TipoVouchers::pdf');
            $routes->post('datos', 'TipoVouchers::datos');
            $routes->post('consulta_detalles', 'TipoVouchers::consulta_detalles');
            $routes->post('consulta_codigo', 'TipoVouchers::consulta_codigo');
            $routes->get('autocompletado', 'TipoVouchers::autoCompletado');

        });

        $routes->group('payment_vouchers', static function($routes) {
        
            $routes->get('index', 'ComprobantesPago::index');
            $routes->get('create', 'ComprobantesPago::create');
            $routes->post('save', 'ComprobantesPago::save');
            $routes->get('edit/(:any)', 'ComprobantesPago::edit/$1');
            $routes->post('update', 'ComprobantesPago::update');
            $routes->get('delete/(:any)', 'ComprobantesPago::delete/$1'); 
            $routes->get('excel', 'ComprobantesPago::excel');
            $routes->get('pdf', 'ComprobantesPago::pdf');
            $routes->post('datos', 'ComprobantesPago::datos');
            $routes->post('consulta_codigo', 'ComprobantesPago::consulta_codigo');

        });

        $routes->group('cost_center', static function($routes) {
        
            $routes->get('index', 'CentroCosto::index');
            $routes->get('create', 'CentroCosto::create');
            $routes->post('save', 'CentroCosto::save');
            $routes->get('edit/(:any)', 'CentroCosto::edit/$1');
            $routes->post('update', 'CentroCosto::update');
            $routes->get('delete/(:any)', 'CentroCosto::delete/$1'); 
            $routes->get('excel', 'CentroCosto::excel');
            $routes->get('pdf', 'CentroCosto::pdf');
            $routes->post('consulta_codigo', 'CentroCosto::consulta_codigo');

        });

        $routes->group('box_banks', static function($routes) {
        
            $routes->get('index', 'CajaBancos::index');
            $routes->get('create', 'CajaBancos::create');
            $routes->post('save', 'CajaBancos::save');
            $routes->get('edit/(:any)', 'CajaBancos::edit/$1');
            $routes->post('update', 'CajaBancos::update');
            $routes->get('delete/(:any)', 'CajaBancos::delete/$1'); 
            $routes->get('excel', 'CajaBancos::excel');
            $routes->get('pdf', 'CajaBancos::pdf');
            $routes->post('autocompletado', 'CajaBancos::autocompletado');

        });

        $routes->group('budget', static function($routes) {
        
            $routes->get('index', 'Presupuesto::index');
            $routes->get('create', 'Presupuesto::create');
            $routes->post('save', 'Presupuesto::save');
            $routes->get('edit/(:any)', 'Presupuesto::edit/$1');
            $routes->post('update', 'Presupuesto::update');
            $routes->get('delete/(:any)', 'Presupuesto::delete/$1'); 
            $routes->get('excel', 'Presupuesto::excel');
            $routes->get('pdf', 'Presupuesto::pdf');
            $routes->post('consulta_codigo', 'Presupuesto::consulta_codigo');

        });

        $routes->group('payment_condition', static function($routes) {
        
            $routes->get('index', 'CondicionesPago::index');
            $routes->get('create', 'CondicionesPago::create');
            $routes->post('save', 'CondicionesPago::save');
            $routes->get('edit/(:any)', 'CondicionesPago::edit/$1');
            $routes->post('update', 'CondicionesPago::update');
            $routes->get('delete/(:any)', 'CondicionesPago::delete/$1'); 
            $routes->get('excel', 'CondicionesPago::excel');
            $routes->get('pdf', 'CondicionesPago::pdf');

        });

        $routes->group('some', static function($routes) {
        
            $routes->get('index', 'Varios::index');
            $routes->get('create', 'Varios::create');
            $routes->post('save', 'Varios::save');
            $routes->get('edit/(:any)', 'Varios::edit/$1');
            $routes->post('update', 'Varios::update');
            $routes->get('delete/(:any)', 'Varios::delete/$1'); 
            $routes->get('excel', 'Varios::excel');
            $routes->get('pdf', 'Varios::pdf');

        });

        $routes->group('exchange_rate', static function($routes) {
        
            $routes->get('index', 'TipoCambio::index');
            $routes->get('edit/(:any)/(:any)', 'TipoCambio::edit/$1/$2');
            $routes->post('update', 'TipoCambio::update');
            $routes->get('excel/(:any)/(:any)', 'TipoCambio::excel/$1/$2');
            $routes->get('pdf/(:any)/(:any)', 'TipoCambio::pdf/$1/$2');
            $routes->post('consulta', 'TipoCambio::consulta');

        });
        
    });

    $routes->group('purchase', static function($routes) {

        $routes->get('create', 'Compras::crear');
    });

});