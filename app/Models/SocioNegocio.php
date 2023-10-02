<?php

namespace App\Models;

use CodeIgniter\Model;

class SocioNegocio extends Model
{
    protected $table = 'socionegocio';

    protected $primaryKey = 'IdSocioN';

    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'CodInterno',
        'CodEmpresa',
        'ApePat',
        'ApeMat',
        'Nom1',
        'Nom2',
        'razonsocial',
        'ruc',
        'docidentidad',
        'direccion1',
        'codubigeo',
        'telefono',
        'direlectronica',
        'comentario',
        'fecingreso',
        'CodTipPer',
        'CodTipoDoc',
        'Idestado',
        'IdCondicion',
        'CodVinculo',
        'pagweb',
        'IdSexo',
        'retencion',
        'CodTipoDoc_Tele',
        'docidentidad_Tele'
    ];

    public function getSocioNegocio(string $CodEmpresa, int $IdSocioN, string $columnas, array $join, string $where, string $orderBy)
    {
        try {
            $result = $this;

            if (!empty($columnas)) $result = $this->select($columnas);

            if (is_array($join) && count($join) > 0) {
                foreach ($join as $indice => $valor) {
                    $result = $result->join($valor['tabla'], $valor['on'], $valor['tipo']);
                }
            }

            $result = $result->where('socionegocio.CodEmpresa', $CodEmpresa);

            if (!empty($IdSocioN)) $result = $result->where('socionegocio.IdSocioN', $IdSocioN);

            if (!empty($where)) $result = $result->where($where);

            if (!empty($orderBy)) $result = $result->orderBy($orderBy);

            $result = $result->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function agregar($data)
    {
        try {
            $this->insert($data);

            $result = $this->insertID();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function actualizar($CodEmpresa, $IdSocioN, $data)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->update($IdSocioN, $data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function eliminar($CodEmpresa, $IdSocioN)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->delete($IdSocioN);

            $socioNegocioXTipoModel = new SocioNegocioXTipo();

            $socioNegocioXTipoModel->where('IdSocioN', $IdSocioN)->delete();

            $socioNegocioBancoModel = new SocioNegocioBanco();

            $socioNegocioBancoModel->where('IdSocion', $IdSocioN)->delete();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * autocompletado de clientes
     */
    public function autoCompletado($busqueda, $tipo, $codEmpresa)
    {
        $this->select("
            socionegocio.idSocioN as id,
            CONCAT(socionegocio.ruc, ' ', socionegocio.razonsocial) AS text
        ");
        $this->table('socionegocio');
        $this->join('socionegocioxtipo', 'socionegocio.idSocioN = socionegocioxtipo.idSocioN', 'inner');
        $this->where('socionegocio.codEmpresa', $codEmpresa);
        $this->where('socionegocioxtipo.CodTipoSN', $tipo);
        if (!empty($busqueda)) {
            $this->groupStart();
            $this->like('socionegocio.razonsocial', $busqueda);
            $this->orLike('socionegocio.ruc', $busqueda);
            $this->groupEnd();
        }
        $this->limit(LIMITE_AUTOCOMPLETADO);
        return $this->get()->getResult();
    }
}
