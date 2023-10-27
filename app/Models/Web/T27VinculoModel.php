<?php

namespace App\Models\Web;

use CodeIgniter\Model;
use Throwable;

class T27VinculoModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'ts27_vinculo';
    protected $primaryKey       = 'CodVinculo';
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'DescVinculo'
    ];

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function autoCompletado($search): array
    {
        try {
            return $this->like('DescVinculo', $search)->get()->getResult();
        } catch (Throwable $e) {
            return [];
        }
    }

}
