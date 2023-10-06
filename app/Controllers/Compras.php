<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Predeterminado;
use App\Models\TipoVoucherCab;
use App\Models\MovimientoCab;
use App\Models\Anexo;
use App\Models\Moneda;

class Compras extends BaseController
{
    public function index()
    {
        //
    }

    public function crear()
    {
        $objPredeterminado = (new Predeterminado())->first('object');
        $objTypeVoucher = null;
        $objOperationType = null;
        $objCurrency = null;
        if (!empty($objPredeterminado)) {
            // Tipo de voucher
            $objTypeVoucher = (new TipoVoucherCab())->find($objPredeterminado->CodTV_co);
            // Tipo de operacion
            $objOperationType = (Object) (new Anexo())->find($objPredeterminado->TipoOperacion_co);
            // Moneda
            $objCurrency = (new Moneda())->find($objPredeterminado->CodMoneda_co);
        }
        // correlativo del movimiento
        $voucher = (new MovimientoCab())->correlativo(date('Y'), date('m'));

        return viewApp('Compra', 'app/compra/crear', compact(
            'objTypeVoucher',
            'voucher',
            'objOperationType',
            'objCurrency'
        ), [], false);
    }
}
