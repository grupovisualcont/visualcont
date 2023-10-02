<?php

namespace App\Models;

use CodeIgniter\Model;

class SocioNegocioXTipo extends Model
{
    protected $table = 'socionegocioxtipo';

    protected $primaryKey = 'CodTipoSN, IdSocioN';

    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'CodTipoSN',
        'IdSocioN'
    ];

    public function getSocioNegocioXTipo($IdSocioN){
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
