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

    public function getSocioNegocio($CodEmpresa)
    {
        try {
            $result = $this->select('socionegocio.*, IF(LENGTH(socionegocio.razonsocial) = 0, CONCAT(socionegocio.Nom1, " ", IF(LENGTH(socionegocio.Nom2) = 0, "", CONCAT(socionegocio.Nom2, " ")), socionegocio.ApePat, " ", socionegocio.ApeMat), socionegocio.razonsocial) AS razonsocial, a1.DescAnexo AS estado, a2.DescAnexo AS condicion')
                ->join('anexos a1', 'a1.IdAnexo = socionegocio.Idestado AND a1.CodEmpresa = socionegocio.CodEmpresa', 'left')
                ->join('anexos a2', 'a2.IdAnexo = socionegocio.IdCondicion AND a2.CodEmpresa = socionegocio.CodEmpresa', 'left')
                ->where('socionegocio.CodEmpresa', $CodEmpresa)
                ->orderBy('socionegocio.idSocioN', 'ASC')
                ->findAll();
            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function eliminarSocioNegocio($CodEmpresa, $IdSocioN)
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

    public function getSocioNegocioPDF($CodEmpresa)
    {
        try {
            $result = $this->select("IdSocioN, IF(ruc = '', CONCAT(ApePat, ' ', ApeMat, ' ', Nom1, IF(LENGTH(Nom2) = 0, '', CONCAT(' ', Nom2))), razonsocial) AS Cliente, ruc, docidentidad, telefono, direccion1")
                ->where('CodEmpresa', $CodEmpresa)
                ->orderBy('idSocioN', 'ASC')
                ->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getSocioNegocioExcel($CodEmpresa)
    {
        try {
            $result = $this->select("IdSocioN, IF(ruc = '', CONCAT(ApePat, ' ', ApeMat, ' ', Nom1, IF(LENGTH(Nom2) = 0, '', CONCAT(' ', Nom2))), razonsocial) AS Cliente, ruc, docidentidad, telefono, direccion1")
                ->where('CodEmpresa', $CodEmpresa)
                ->orderBy('idSocioN', 'ASC')
                ->findAll();

            return $result;
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
