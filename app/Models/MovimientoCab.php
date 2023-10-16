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

    public function getMovimientoCab(string $CodEmpresa, int $IdMov, string $columnas, array $join, array $parametros, string $where, string $orderBy)
    {
        try {
            $result = $this;

            if (!empty($columnas)) $result = $this->select($columnas);

            if (is_array($join) && count($join) > 0) {
                foreach ($join as $indice => $valor) {
                    $result = $result->join($valor['tabla'], $valor['on'], $valor['tipo']);
                }
            }

            $result = $result->where('movimientocab.CodEmpresa', $CodEmpresa);

            if (!empty($IdMov)) $result = $result->where('movimientocab.IdMov', $IdMov);

            if (is_array($parametros) && count($parametros) > 0) {

                foreach ($parametros as $indice => $valor) {
                    if (isset($valor['Periodo']) && !empty($valor['Periodo'])) $result = $result->where('movimientocab.Periodo', $valor['Periodo']);

                    if (isset($valor['Mes']) && !empty($valor['Mes'])) $result = $result->where('movimientocab.Mes', $valor['Mes']);

                    if (isset($valor['Codmov']) && !empty($valor['Codmov'])) $result = $result->where('movimientocab.Codmov', $valor['Codmov']);

                    if (isset($valor['CodTV']) && !empty($valor['CodTV'])) $result = $result->where('movimientocab.CodTV', $valor['CodTV']);

                    if (isset($valor['IdMovRef']) && !empty($valor['IdMovRef'])) $result = $result->where('movimientocab.IdMovRef', $valor['IdMovRef']);

                    if (isset($valor['IdMovAplica']) && !empty($valor['IdMovAplica'])) $result = $result->where('movimientocab.IdMovAplica', $valor['IdMovAplica']);

                    if (isset($valor['FecContable']) && !empty($valor['FecContable'])) $result = $result->where('DATE(movimientocab.FecContable)', $valor['FecContable']);

                    if (isset($valor['Origen']) && !empty($valor['Origen'])) $result = $result->where('movimientocab.Origen IN ("' . implode('","', $valor['Origen']) . '")');
                }
            }

            if (!empty($where)) $result = $result->where($where);

            if (!empty($orderBy)) $result = $result->orderBy($orderBy);

            $result = $result->asArray()->findAll();

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

    public function actualizar($CodEmpresa, $IdMov, $data)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->update($IdMov, $data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function eliminar($CodEmpresa, $Importado, $IdMov)
    {
        try {
            $result = $this->where('movimientocab.CodEmpresa', $CodEmpresa);

            if (!empty($Importado)) {
                $result = $result->where('movimientocab.Importado', $Importado)->delete();
            } else {
                $result = $result->delete($IdMov);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

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
