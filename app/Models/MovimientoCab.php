<?php

namespace App\Models;

use CodeIgniter\Model;

class MovimientoCab extends Model
{
    protected $table = 'movimientocab';
    protected $primaryKey = 'IdMov';
    protected $returnType = 'object';
    protected $allowedFields = [
        'IdMov',
        'CodEmpresa',
        'Periodo',
        'Mes',
        'Codmov',
        'CodTV',
        'IdMovRef',
        'IdMovAplica',
        'FecContable',
        'TotalSol',
        'TotalDol',
        'Origen',
        'Glosa',
        'Estado',
        'Importado',
        'codOtroSis',
        'ValorTC',
        'Detraccion',
        'FlagInterno'
    ];


    public function correlativo($periodo, $mes)
    {
        $this->select('movimientocab.Codmov');
        $this->table('movimientocab');
        $this->join('tipovouchercab', 'movimientocab.CodTV = tipovouchercab.CodTV', 'inner');
        $this->where("LEFT(movimientocab.Codmov, LENGTh(movimientocab.Codmov)-8) = 'COM'", null, false);
        $this->where('Periodo', $periodo);
        $this->where('movimientocab.Mes', $mes);
        $this->orderBy('movimientocab.CodMov', 'desc');
        $resultado = $this->first();
        $codMov = (empty($resultado)) ? 'COM000000' : $resultado->Codmov;
        $numero = substr($codMov, 3, strlen($codMov));
        $sigNumero = intval($numero) + 1;
        return 'COM' . str_pad($sigNumero, strlen($numero), '0', STR_PAD_LEFT);
    }
}
