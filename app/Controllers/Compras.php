<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Predeterminado;
use App\Models\TipoVoucherCab;
use App\Models\MovimientoCab;

class Compras extends BaseController
{
    public function index()
    {
        //
    }

    public function crear()
    {
        // Tipo de voucher
        $objTypeVoucher = null;
        $typeVoucher = (new Predeterminado())->first('object')->CodTV_co;
        if (!empty($typeVoucher)) {
            $objTypeVoucher = (new TipoVoucherCab())->find($typeVoucher);
        }
        $voucher = (new MovimientoCab())->correlativo(date('Y'), date('m'));

        return viewApp('Compra', 'app/compra/crear', compact(
            'objTypeVoucher',
            'voucher'
        ));
    }
}
