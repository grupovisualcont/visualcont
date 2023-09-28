/*
 * @version 1.0.0
 */

const objPurchaseCab = {};

$(function() {

});

$(document).ready(function() {

    s2SocioNegocio.init();
    s2TipoVoucher.init();
    s2CondicionPago.init();
    s2Anexo.urlMethod = 'operation_type';
    s2Anexo.init($('#operation_type'));
    s2Moneda.init();

});