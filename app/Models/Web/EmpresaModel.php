<?php

namespace App\Models\Web;

use CodeIgniter\Model;
use Faker\Generator;

class EmpresaModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'empresa';
    protected $primaryKey       = 'CodEmpresa';
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'CodEmpresa',
        'RazonSocial',
        'Ruc',
        'Contrasenia',
        'Carpeta',
        'Titulo1',
        'Titulo2',
        'Titulo3',
        'AnioInicio',
        'NumNiveles',
        'Mascara',
        'RutaReporte',
        'CodTipoDoc',
        'CarpetaRpt',
        'CodPC',
        'CodUbigeo',
        'Direccion',
        'Telefono',
        'RepresLegal',
        'Estado',
        'Modulo',
        'Tipo_Conta',
        'Reg_Renta',
        'Grupo_Crono',
        'Token_Ver',
        'URL_Conex',
        'Autorizacion',
        'Key_SI',
        'TokenWeb',
        'Emp_id',
        'Emp_Secret',
        'Fec_Act',
        'Enviar_Solo_SI',
    ];

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function login($usuario, $password)
    {
        try {
            $result = $this->where('CodEmpresa', $usuario)
                ->where('Contrasenia', $password)
                ->first();
            return $result;
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function getPredeterminado($CodEmpresa)
    {
        try {
            return $this->select('*')
                ->table('predeterminado')
                ->where('CodEmpresa', $CodEmpresa)
                ->first();
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function fake(Generator &$faker)
    {
        return [
            'CodInterno'  => $faker->randomDigit(),
            'CodEmpresa'  => 'VASESP',
            'ApePat'  => $faker->lastName(),
            'ApeMat' => $faker->lastName(),
            'Nom1' => $faker->name(),
            'Nom2' => $faker->name(),
            'DocIdentidad' => $faker->numberBetween(10000001, 99999999),
            'Ruc' => $faker->numberBetween(10000000001, 99999999999),
            'Direccion1' => 'address',
            'CodEstado' => null,
            'CodHistImp' => null,
            'CodTipPer' => null,
            'CodUbigeo' => null,
            'CodUbigeoContacto' => null,
            'CodTipoDoc' => null,
        ];
    }

}
