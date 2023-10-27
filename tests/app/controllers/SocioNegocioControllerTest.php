<?php

namespace App\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\ControllerTestTrait;
use App\Models\Web\SocioNegocioModel;

/**
 * @internal
 */
final class SocioNegocioModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use ControllerTestTrait;

    protected $DBGroup      = 'tests';
    protected $migrate      = true;
    protected $migrateOnce  = true;
    protected $refresh      = true;
    protected $namespace    = null;

    protected $seedOnce     = true;
    protected $seed         = 'DefaultDataSeeder';
    protected $basePath     = null;

    protected function setUp(): void
    {
        parent::setUp();
        // Preload any models, libraries, etc, here.
        $session = \Config\Services::session();
        $session->set('empresa', 'VASESP');
        helper('master');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function testNuevoSocioNegocioFormulario()
    {
        $result = $this->controller(\App\Controllers\Web\SocioNegocioController::class)
            ->execute('crear');

        $this->assertTrue($result->isOK());
    }

    public function testNuevoSocioNegocioValidarCamposObligatorios()
    {
        $this->request->setHeader('Content-Type', 'application/json');
        $result = $this->withBody(json_encode([]))
            ->controller(\App\Controllers\Web\SocioNegocioController::class)
            ->execute('store');
        $result->assertJSONExact(json_encode([
            'status' => 422,
            'message' => '',
            'errors' => [
                [
                    'form_id' => 'fecingreso',
                    'message' => 'Fecha de Ingreso no puede estar vacio.',
                ],
                [
                    'form_id' => 'IdCondicion',
                    'message' => 'Condición no puede estar vacio.',
                ],
                [
                    'form_id' => 'Idestado',
                    'message' => 'Estado no puede estar vacio.',
                ],
                [
                    'form_id' => 'CodTipoDoc',
                    'message' => 'Debe elegir un tipo documento.',
                ],
            ],
            'data' => [],
        ]));
    }

    public function testNuevoSocioNegocioValidarFechaIngreso()
    {
        $this->request->setHeader('Content-Type', 'application/json');
        $result = $this->withBody(json_encode(['IdCondicion' => 1, 'Idestado' => 1, 'CodTipoDoc' => '-']))
            ->controller(\App\Controllers\Web\SocioNegocioController::class)
            ->execute('store');
        $result->assertJSONExact(json_encode([
            'status' => 422,
            'message' => '',
            'errors' => [
                [
                    'form_id' => 'fecingreso',
                    'message' => 'Fecha de Ingreso no puede estar vacio.',
                ],
            ],
            'data' => [],
        ]));
    }

    public function testNuevoSocioNegocioValidarCodicion()
    {
        $this->request->setHeader('Content-Type', 'application/json');
        $result = $this->withBody(json_encode(['fecingreso' => '24/10/2023', 'Idestado' => 1, 'CodTipoDoc' => '-']))
            ->controller(\App\Controllers\Web\SocioNegocioController::class)
            ->execute('store');
        $result->assertJSONExact(json_encode([
            'status' => 422,
            'message' => '',
            'errors' => [
                [
                    'form_id' => 'IdCondicion',
                    'message' => 'Condición no puede estar vacio.',
                ],
            ],
            'data' => [],
        ]));
    }

    public function testNuevoSocioNegocioValidarEstado()
    {
        $this->request->setHeader('Content-Type', 'application/json');
        $result = $this->withBody(json_encode(['fecingreso' => '24/10/2023', 'IdCondicion' => 1, 'CodTipoDoc' => '-']))
            ->controller(\App\Controllers\Web\SocioNegocioController::class)
            ->execute('store');
        $result->assertJSONExact(json_encode([
            'status' => 422,
            'message' => '',
            'errors' => [
                [
                    'form_id' => 'Idestado',
                    'message' => 'Estado no puede estar vacio.',
                ],
            ],
            'data' => [],
        ]));
    }

    public function testNuevoSocioNegocioValidarTipoSN()
    {
        $this->request->setHeader('Content-Type', 'application/json');
        $result = $this->withBody(json_encode(['fecingreso' => '24/10/2023', 'IdCondicion' => 1, 'Idestado' => 1, 'CodTipoDoc' => '-']))
            ->controller(\App\Controllers\Web\SocioNegocioController::class)
            ->execute('store');
        $result->assertJSONExact(json_encode([
            'status' => 422,
            'message' => '',
            'errors' => [
                [
                    'form_id' => 'tipo_socio_negocio[0]',
                    'message' => 'Debes elegir un tipo Socio Negocio.',
                ],
            ],
            'data' => [],
        ]));
    }

    public function testNuevoSocioNegocioValidarTipoSNExiste()
    {
        $this->request->setHeader('Content-Type', 'application/json');
        $result = $this->withBody(json_encode(['fecingreso' => '24/10/2023', 'IdCondicion' => 1, 'Idestado' => 1, 'CodTipoDoc' => '-',
            'tipo_socio_negocio' => ['20']
        ]))
            ->controller(\App\Controllers\Web\SocioNegocioController::class)
            ->execute('store');
        $result->assertJSONExact(json_encode([
            'status' => 422,
            'message' => '',
            'errors' => [
                [
                    'form_id' => 'tipo_socio_negocio[0]',
                    'message' => 'Tipo Socio Negocio no pudo ser encontrado.',
                ],
            ],
            'data' => [],
        ]));
    }

    public function testNuevoSocioNegocio()
    {
        $this->request->setHeader('Content-Type', 'application/json');
        $result = $this->withBody(json_encode(['fecingreso' => '24/10/2023', 'IdCondicion' => 1, 'Idestado' => 1, 'CodTipoDoc' => '1',
            'tipo_socio_negocio' => [
                '1'
            ]
        ]))
            ->controller(\App\Controllers\Web\SocioNegocioController::class)
            ->execute('store');
        $result->assertJSONExact(json_encode([
            'status' => 200,
            'message' => 'Registrado correctamente.',
            'errors' => [],
            'data' => [],
        ]));
    }

}
