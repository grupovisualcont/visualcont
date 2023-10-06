<?php

namespace App\Models;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Model;

class Ubigeo extends Model
{
    protected $table = 'ubigeo';

    protected $primaryKey = 'codubigeo';

    protected $allowedFields = [];

    public function getUbigeo(string $codubigeo, string $columnas, array $join, string $where, string $orderBy)
    {
        try {
            $result = $this;

            if (!empty($columnas)) $result = $this->select($columnas);

            if (is_array($join) && count($join) > 0) {
                foreach ($join as $indice => $valor) {
                    $result = $result->join($valor['tabla'], $valor['on'], $valor['tipo']);
                }
            }

            if (!empty($IdSocioN)) $result = $result->where('codubigeo', $codubigeo);

            if (!empty($where)) $result = $result->where($where);

            if (!empty($orderBy)) $result = $result->orderBy($orderBy);

            $result = $result->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getUbigeoQuery(BaseConnection $db, $codubigeo, $descubigeo)
    {
        try {
            $where = '';

            $like = '';

            if (!empty($codubigeo)) $where = ' AND dist.codubigeo = "' . $codubigeo . '"';

            if (!empty($descubigeo)) $like = ' AND (dept.descubigeo LIKE "%' . $descubigeo . '%" OR prov.descubigeo LIKE "%' . $descubigeo . '%" OR dist.descubigeo LIKE "%' . $descubigeo . '%")';

            $result = $db->query('SELECT dist.codubigeo AS id, (
                        SELECT (
                            SELECT CONCAT(dept.descubigeo, " \\\ ", prov.descubigeo, " \\\ ", dist.descubigeo) AS descripcion 
                            FROM ubigeo dept WHERE dept.codubigeo = SUBSTRING(prov.codubigeo, 1, 4) ' . $like . '
                        )
                        FROM ubigeo prov
                        WHERE prov.codubigeo = SUBSTRING(dist.codubigeo, 1, 6)
                    )
                    AS text
                    FROM ubigeo dist
                    WHERE LENGTH(dist.codubigeo) = 9 AND LENGTH(dist.codubigeo) != 2 AND dist.codubigeo NOT LIKE "9%"
                    ' . $where)
                ->getResult();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
