<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class SocioNegocio extends Entity
{
    protected $attributes = [
        'CodInterno'            => null,
        'CodEmpresa'            => null,
        'ApePat'                => null,
        'ApeMat'                => null,
        'Nom1'                  => null,
        'Nom2'                  => null,
        'RazonSocial'           => null,
        'Ruc'                   => null,
        'DocIdentidad'          => null,
        'Direccion1'            => null,
        'Direccion2'            => null,
        'CodUbigeo'             => null,
        'CodVinculo'            => null,
        'Telefono'              => null,
        'Fax'                   => null,
        'PagWeb'                => null,
        'DirElectronica'        => null,
        'Comentario'            => null,
        'ContactoNombre'        => null,
        'ContactoEmail'         => null,
        'ContactoTelefono'      => null,
        'LineaCredito'          => null,
        'FecIngreso'            => null,
        'Retencion'             => null,
        'Percepcion'            => null,
        'CodTipPer'             => null,
        'CodTipoDoc'            => null,
        'NomComercial'          => null,
        'CodEstado'             => null,
        'CodCondicion'          => null,
        'CodSexo'               => null,
        'CodTipoCliente'        => null,
        'CodUbigeoContacto'     => null,
        'CodHistImp'            => null,
        'CodTipoDoc_Tele'       => null,
        'DocIdentidad_Tele'     => null,
        'CodSocioN_Ven'         => null,
        'IdZona'                => null,
        'IdCargo'               => null,
        'IdListaP'              => null,
        'CodCCosto'             => null,
        'IdTurno'               => null,
        'VtaCredito'            => null,
        'FlagRetencion'         => null,
        'FlagEnvioCorreoFe'     => null,
    ];

    protected $datamap = [];
    protected $dates   = [];
    protected $casts   = [
        'FecIngreso'        => '?string',
        'CodCondicion'      => '?integer',
        'CodEstado'         => '?integer',
        'CodHistImp'        => '?integer',
        'CodUbigeo'         => '?string|null',
        'CodUbigeoContacto' => '?string',
        'Ruc'               => '?string',
    ];

    public function setFecIngreso(string|null $value)
    {
        if (!empty($value)) {
            $ex = explode('/', $value);
            $this->attributes['FecIngreso'] = $ex[2] . '-' . $ex[1] . '-' . $ex[0];
        }
        return $this;
    }

    public function getFecIngreso()
    {
        if (!empty($this->attributes['FecIngreso'])) {
            return $this->attributes['FecIngreso'] = date('d/m/Y', strtotime($this->attributes['FecIngreso']));
        }
        return $this->attributes['FecIngreso'];
    }

}
