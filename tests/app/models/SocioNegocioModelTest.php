<?php

namespace App\Models;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\Web\SocioNegocioModel;

/**
 * @internal
 */
final class SocioNegocioModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

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
        // Se crear un socio de negocio
        fake(SocioNegocioModel::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function testValidarDocIdentidadExiste()
    {
        // Expect
        $objSocioNegocio = (new SocioNegocioModel())->first();
        // Find
        $objNewSocioNegocio = new SocioNegocioModel();

        $resp = $objNewSocioNegocio->validarDocIdentidad('VASESP', $objSocioNegocio->DocIdentidad);
        $this->assertTrue(!empty($resp));
    }

    // @Revisar
    public function testValidarRucExiste()
    {
        // Expect
        $objSocioNegocio = (new SocioNegocioModel())->first();
        // Find
        $objNewSocioNegocio = new SocioNegocioModel();

        $resp = $objNewSocioNegocio->validarRuc('VASESP', $objSocioNegocio->Ruc);
        $this->assertTrue(!empty($resp));
    }

    public function testValidarRazonSocialExiste()
    {
        // Expect
        $objSocioNegocio = (new SocioNegocioModel())->first();
        // Find
        $objNewSocioNegocio = new SocioNegocioModel();

        $resp = $objNewSocioNegocio->validarRazonSocial('VASESP', $objSocioNegocio->RazonSocial);
        $this->assertTrue(!empty($resp));
    }

}
