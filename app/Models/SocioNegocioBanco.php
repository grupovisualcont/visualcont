<?php

namespace App\Models;

use CodeIgniter\Model;

class SocioNegocioBanco extends Model
{
    protected $table = 'socionegociobancos';

    protected $primaryKey = 'IdCtaCte, IdSocion';

    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'IdSocion',
        'CodBanco',
        'idTipoCuenta',
        'NroCuenta',
        'NroCuentaCCI',
        'Predeterminado'
    ];

    public function getSocioNegocioBanco($IdSocioN){
        try {
            $result = $this->where('IdSocioN', $IdSocioN)->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function agregar($data){
        try {
            $this->insert($data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function eliminar($IdSocioN){
        try {
            $this->where('IdSocioN', $IdSocioN)->delete();
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
