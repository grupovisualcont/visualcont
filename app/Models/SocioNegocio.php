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
}
