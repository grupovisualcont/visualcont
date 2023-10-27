<?php

namespace App\Models\Web;

use CodeIgniter\Model;
use App\Entities\SocioNegocio;
use Faker\Generator;
use Throwable;

/**
 * @Class SocioNegocio
 */
class SocioNegocioModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'socionegocio';
    protected $primaryKey       = 'CodSocioN';
    protected $returnType       = SocioNegocio::class;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'CodInterno',
        'CodEmpresa',
        'CodUbigeo',
        'CodTipPer',
        'CodTipoDoc',
        'CodCCosto',
        'CodEstado',
        'CodCondicion',
        'CodSexo',
        'CodHistImp',
        'CodVinculo',
        'ApePat',
        'ApeMat',
        'Nom1',
        'Nom2',
        'RazonSocial',
        'Ruc',
        'DocIdentidad',
        'Direccion1',
        'Direccion2',
        'Telefono',
        'Fax',
        'PagWeb',
        'DirElectronica',
        'Comentario',
        'ContactoNombre',
        'ContactoEmail',
        'ContactoTelefono',
        'LineaCredito',
        'FecIngreso',
        'Retencion',
        'Percepcion',
        'NomComercial',
        'CodTipoCliente',
        'CodUbigeoContacto',
        'CodTipoDoc_Tele',
        'DocIdentidad_Tele',
        'CodSocioN_Ven',
        'IdZona',
        'IdCargo',
        'IdListaP',
        'IdTurno',
        'VtaCredito',
        'FlagRetencion',
        'FlagEnvioCorreoFe',
    ];

    // Validation
    protected $validationRules      = [
        'FecIngreso'    => 'required',
        'CodCondicion'  => 'required',
        'CodEstado'     => 'required',
        'CodTipoDoc'    => 'required',
    ];
    protected $validationMessages   = [
        'FecIngreso' => [
            'required' => 'Fecha de Ingreso no puede estar vacio.|fecingreso',
        ],
        'CodCondicion' => [
            'required' => 'Condición no puede estar vacio.|IdCondicion',
        ],
        'CodEstado' => [
            'required' => 'Estado no puede estar vacio.|Idestado',
        ],
        'CodTipoDoc' => [
            'required' => 'Debe elegir un tipo documento.|CodTipoDoc',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function lista(string $CodEmpresa): array
    {
        try {
            $this->select("
                socionegocio.*, 
                IF(LENGTH(socionegocio.RazonSocial) = 0 OR socionegocio.RazonSocial IS NULL, CONCAT(socionegocio.Nom1, ' ', IF(LENGTH(socionegocio.Nom2) = 0, '', CONCAT(socionegocio.Nom2, ' ')), socionegocio.ApePat, ' ', socionegocio.ApeMat), socionegocio.RazonSocial) AS SocioNegocioNombre, 
                a1.DescAnexo AS Estado, 
                a2.DescAnexo AS Condicion
            ");
            $this->where('socionegocio.CodEmpresa', $CodEmpresa);
            $this->join('anexo a1', 'a1.CodAnexo = socionegocio.CodEstado', 'left');
            $this->join('anexo a2', 'a2.CodAnexo = socionegocio.CodCondicion', 'left');
            $this->orderBy('socionegocio.CodSocioN DESC');
            return $this->get()
                ->getResult('array');
        } catch (Throwable $ex) {
            return [];
        }
    }

    /**
     * Validar si el ruc en la empresa asignada
     * @return mixed
     */
    public function validarRuc(string $CodEmpresa, string $ruc, string $omitirRuc = ''): mixed
    {
        try {
            $this->select('*');
            $this->where('CodEmpresa', $CodEmpresa);
            $this->where('Ruc', $ruc);
            if (!empty($omitirRuc)) {
                $this->where('Ruc !=', $omitirRuc);
            }
            return $this->get()
                ->getRow();
        } catch (Throwable $ex) {
            return null;
        }
    }

    /**
     * Validar si el ruc en la empresa asignada
     * @return mixed
     */
    public function validarDocIdentidad(string $CodEmpresa, string $docIdentidad, string $omitirDocIdentidad = ''): mixed
    {
        try {
            $this->select('*');
            $this->where('CodEmpresa', $CodEmpresa);
            $this->where('DocIdentidad', $docIdentidad);
            if (!empty($omitirDocIdentidad)) {
                $this->where('DocIdentidad !=', $omitirDocIdentidad);
            }
            return $this->get()
                ->getRow();
        } catch (Throwable $ex) {
            return null;
        }
    }

    /**
     * Validar si el ruc en la empresa asignada
     * @return mixed
     */
    public function validarRazonSocial(string $CodEmpresa, string|null $RazonSocial, string $omitirRazonSocial = ''): mixed
    {
        try {
            $this->select('*');
            $this->where('CodEmpresa', $CodEmpresa);
            $this->where('RazonSocial', $RazonSocial);
            if (!empty($omitirRazonSocial)) {
                $this->where('RazonSocial !=', $omitirRazonSocial);
            }
            return $this->get()
                ->getRow();
        } catch (Throwable $ex) {
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
            'CodEstado' => 1,
            'CodCondicion' => 1,
            'FecIngreso' => '24/10/2023',
            'CodHistImp' => null,
            'CodTipPer' => null,
            'CodUbigeo' => null,
            'CodUbigeoContacto' => null,
            'CodTipoDoc' => 1,
        ];
    }

}
