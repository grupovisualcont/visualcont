autocompletado('#CodTV', { Tipo: [1, 2], App: 'Ventas' }, BASE_URL + 'app/mantenience/types_of_vouchers/autocompletado_');
autocompletado('#CodCondPago', { Tipo: 168, App: 'Ventas' }, BASE_URL + 'app/mantenience/payment_condition/autocompletado_');
autocompletado('#CodMoneda', { App: 'Ventas' }, BASE_URL + 'app/moneda/autocompletado_');
autocompletado('#TipoOperacion', { IdAnexo: 0, TipoAnexo: 5, OtroDato: '', App: 'Ventas' }, BASE_URL + 'app/attached/autocompletado');
autocompletado('#IdSocioN', { verDocumento: 'true', App: 'Ventas' }, BASE_URL + 'app/mantenience/business_partner/autocompletado_');
autocompletado('#CodDocumento', { App: 'Ventas' }, BASE_URL + 'app/documento/autocompletado');
autocompletado('#CodTipPer', {}, BASE_URL + 'app/type_person/autocompletado');
autocompletado('#CodTipoDoc', { tipo: 'documento' }, BASE_URL + 'app/identity_document_type/autocompletado');
autocompletado('#IdCondicion', { IdAnexo: 0, TipoAnexo: 2, OtroDato: '', App: 'Ventas' }, BASE_URL + 'app/attached/autocompletado');
autocompletado('#CodCuentaBanco', { tipo: 'banco', App: 'Ventas' }, BASE_URL + 'app/mantenience/accounting_plan/autocompletado');
autocompletado('#CodTipoPagoBanco', { App: 'Ventas' }, BASE_URL + 'app/tipoPago/autocompletado');

autocompletado('select.CodCuenta', { App: 'Ventas' }, BASE_URL + 'app/mantenience/accounting_plan/autocompletado');
autocompletado('select.CodMoneda', { text: 'Abrev', App: 'Ventas' }, BASE_URL + 'app/moneda/autocompletado_');
autocompletado('select.IdSocioN', { verDocumento: 'true', App: 'Ventas' }, BASE_URL + 'app/mantenience/business_partner/autocompletado_');
autocompletado('select.CodDocumento', { App: 'Ventas' }, BASE_URL + 'app/documento/autocompletado');
autocompletado('select.TipoOperacion', { IdAnexo: 0, TipoAnexo: 5, OtroDato: '', App: 'Ventas' }, BASE_URL + 'app/attached/autocompletado');
autocompletado('select.CodCcosto', { App: 'Ventas' }, BASE_URL + 'app/mantenience/cost_center/autocompletado');
autocompletado('select.CodCondPago', { App: 'Ventas' }, BASE_URL + 'app/mantenience/payment_condition/autocompletado_');
autocompletado('select.Parametro', { App: 'Ventas' }, BASE_URL + 'app/parametro/autocompletado');
autocompletado('select.IdDetraccion', { App: 'Ventas' }, BASE_URL + 'app/detraccion/autocompletado');
autocompletado('select.IdTipOpeDetra', { IdAnexo: 0, TipoAnexo: 23, OtroDato: '', App: 'Ventas' }, BASE_URL + 'app/attached/autocompletado');
autocompletado('select.Declarar_Per', { App: 'Ventas' }, BASE_URL + 'app/declararPeriodo/autocompletado');
autocompletado('select.Declarar_Est', { IdAnexo: 0, TipoAnexo: 11, OtroDato: '', App: 'Ventas' }, BASE_URL + 'app/attached/autocompletado');
autocompletado('select.IdActivo', { App: 'Ventas' }, BASE_URL + 'app/mantenience/fixed_assets/autocompletado');

set_suma_total();

set_suma_total_referencia();

verificar_botones_Referencia();

parametros_codTV();

var Tipo = $('#CodTV option:selected').attr('data-tipo');

if (Tipo == 1) {
    autocompletado('#FormaPago', { IdAnexo: 0, TipoAnexo: 6, OtroDato: '', App: 'Ventas' }, BASE_URL + 'app/attached/autocompletado');

    $('#FormaPago').removeAttr('disabled');
} else if (Tipo == 2) {
    $('#FormaPago').html('<option value="NINGUNO">NINGUNO</option>');
    $('#FormaPago').attr('disabled', true);
}

var IdReferenciaManual = 0;
var id_set_total = '';
var afecto_tmp = 0;
var isc_tmp = 0;
var igv_tmp = 0;
var igv = 0;
var total_tmp = 0;

function verificar_botones_Referencia() {
    var CodDocumento = $('#CodDocumento option:selected').val();

    if (notas_credito.some(item => item.CodDocumento === CodDocumento) && Importado == 0) {
        $('#btnReferenciaExistente').removeAttr('disabled');
        $('#btnReferenciaExistente').attr('onclick', 'consulta_notas_credito()');
        $('#btnReferenciaManual').removeAttr('disabled');
        $('#btnReferenciaManual').attr('onclick', 'seleccionar_movimiento_manual()');
        $('#btnQuitarReferencia').removeAttr('disabled');
    } else if (Importado == 1) {
        $('#btnReferenciaExistente').attr('disabled', true);
        $('#btnReferenciaExistente').removeAttr('onclick');
        $('#btnReferenciaManual').attr('disabled', true);
        $('#btnReferenciaManual').removeAttr('onclick');
        $('#btnQuitarReferencia').attr('disabled', true);
    } else {
        $('#btnReferenciaExistente').attr('disabled', true);
        $('#btnReferenciaExistente').removeAttr('onclick');
        $('#btnReferenciaManual').attr('disabled', true);
        $('#btnReferenciaManual').removeAttr('onclick');
        $('#btnQuitarReferencia').attr('disabled', true);
    }
}

function parametros_codTV() {
    var CodTV = $('#CodTV option:selected').val();

    $.ajax({
        url: BASE_URL + 'app/movements/sales/parametros_CodTV',
        data: {
            CodTV,
        },
        type: 'POST',
        success: function (data) {
            var datos = JSON.parse(data);

            $('#labelAfecto').html('-');
            $('#labelInafecto').html('-');
            $('#labelExonerado').html('-');
            $('#labelDescuento').html('-');
            $('#labelAnticipo').html('-');
            $('#labelISC').html('-');
            $('#labelIgv').html('-');
            $('#labelTotal').html('-');

            $('#labelAfecto').html(datos.Afecto);
            $('#labelInafecto').html(datos.Inafecto);
            $('#labelExonerado').html(datos.Exonerado);
            $('#labelDescuento').html(datos.Descuento);
            $('#labelAnticipo').html(datos.Anticipo);
            $('#labelISC').html(datos.Isc);
            $('#labelIgv').html(datos.Igv);
            $('#labelTotal').html(datos.Total);
        },
    });
}

function verificar_tipo_documento_identidad() {
    var CodTipPer = $('#CodTipPer option:selected') ? $('#CodTipPer option:selected').val() : '';
    var CodTipoDoc = $('#CodTipoDoc option:selected') ? $('#CodTipoDoc option:selected').val() : '';
    var TipoDato = $('#CodTipoDoc option:selected').attr('data-tipo-dato');

    if (TipoDato) TipoDato = TipoDato.split('|');

    if (CodTipPer == datos_ruc_CodTipPer) {
        $('#ApePat').val('');
        $('#ApePat').attr('readonly', true);
        $('#ApeMat').val('');
        $('#ApeMat').attr('readonly', true);
        $('#Nom1').val('');
        $('#Nom1').attr('readonly', true);
        $('#Nom2').val('');
        $('#Nom2').attr('readonly', true);
        $('#razonsocial').attr('readonly', false);
    } else {
        if (CodTipPer != '03') {
            $('#razonsocial').val('');
            $('#razonsocial').attr('readonly', true);
        }

        $('#razonsocial').val('');
        $('#razonsocial').attr('readonly', true);
        $('#ApePat').attr('readonly', false);
        $('#ApeMat').attr('readonly', false);
        $('#Nom1').attr('readonly', false);
        $('#Nom2').attr('readonly', false);
    }

    if (CodTipoDoc == '-') {
        $('#ruc').val('');
        $('#docidentidad').val('');
        $('#razonsocial').attr('readonly', false);
    }

    if (TipoDato.length == 3) {
        var id = '';

        var longitud = TipoDato[0];
        var tipo_dato = TipoDato[1];
        var tipo_longitud = TipoDato[2];

        if (CodTipoDoc == '6') {
            id = 'ruc';
            id_aux = 'docidentidad';
        } else {
            id = 'docidentidad';
            id_aux = 'ruc';
        }

        $('#' + id).removeAttr('onkeypress');
        $('#' + id).removeAttr('minlength');
        $('#' + id).removeAttr('maxlength');
        $('#' + id_aux).removeAttr('onkeypress');
        $('#' + id_aux).removeAttr('minlength');
        $('#' + id_aux).removeAttr('maxlength');

        if (tipo_longitud == 'F') {
            if (tipo_dato == 'N') {
                $('#' + id).attr('oninput', 'verificar_longitud_documento(this)');
                $('#' + id).attr('onkeypress', 'esNumero(event)');
            }
        }

        if (tipo_longitud == 'V') {
            $('#' + id).attr('maxlength', longitud);
        }

        if (tipo_dato == 'N') {
            $('#' + id).attr('type', 'text');
        }

        if (tipo_dato == 'A') {
            $('#' + id).attr('type', 'text');
        }
    }
}

function verificar_longitud_documento(item) {
    var CodTipoDoc = $('#CodTipoDoc option:selected').val();

    if (CodTipoDoc == '-') {
        $('#ruc').val('');
        $('#docidentidad').val('');
    } else {
        if (item.id == 'ruc') {
            $('#docidentidad').val('');

            if (item.value.length > 11) {
                alertify.alert('Debe Ingresar hasta 11 dígitos!', function () { }).set({ title: 'Contabilidad' });
            }

            item.value = item.value.substr(0, 11);
        } else if (item.id == 'docidentidad') {
            $('#ruc').val('');

            if (item.value.length > 8) {
                alertify.alert('Debe Ingresar hasta 8 dígitos!', function () { }).set({ title: 'Contabilidad' });
            }

            item.value = item.value.substr(0, 8);
        }
    }
}

function consulta_sunat(tipo_documento) {
    var numero_documento = '';
    var longitud = 0;

    if (tipo_documento == 'ruc') {
        numero_documento = $('#ruc').val();
        longitud = 11;
    } else {
        numero_documento = $('#docidentidad').val();
        longitud = 8;
    }

    if (numero_documento.length == 8 || numero_documento.length == 11) {
        $.ajax({
            url: BASE_URL + 'empresa/consulta_sunat',
            data: {
                tipo_documento,
                numero_documento,
            },
            type: 'POST',
            success: function (data) {
                if (data == 'null') {
                    alertify.error('Número de documento inválido');
                } else {
                    var datos = JSON.parse(data);

                    if (datos.error == null) {
                        if (tipo_documento == 'ruc') {
                            nuevo_option('#CodTipPer', { CodTipPer: '02' }, BASE_URL + 'app/type_person/autocompletado');

                            $('#docidentidad').val('');
                        } else {
                            nuevo_option('#CodTipPer', { CodTipPer: '01' }, BASE_URL + 'app/type_person/autocompletado');

                            $('#ruc').val('');
                        }

                        if (datos.apellidoPaterno) {
                            $('#ApePat').val(datos.apellidoPaterno.toLowerCase());
                            $('#ApePat').css('textTransform', 'capitalize');
                        }

                        if (datos.apellidoMaterno) {
                            $('#ApeMat').val(datos.apellidoMaterno.toLowerCase());
                            $('#ApeMat').css('textTransform', 'capitalize');
                        }

                        if (datos.nombres) {
                            $('#Nom1').val(datos.nombres.toLowerCase());
                            $('#Nom1').css('textTransform', 'capitalize');
                        }

                        nuevo_option('#CodTipoDoc', { tipo: 'documento', CodTipoDoc: datos.tipoDocumento }, BASE_URL + 'app/identity_document_type/autocompletado');

                        if (datos.nombre) {
                            $('#razonsocial').val(datos.nombre);
                        }

                        var condiciones = $('#IdCondicion')[0];

                        var condicion = '';

                        for (let index = 0; index < condiciones.length; index++) {
                            if (condiciones[index].attributes[1].value.toLowerCase() == datos.condicion.toLowerCase()) {
                                condicion = condiciones[index].value;
                                index = condiciones.length;
                            }
                        }

                        if (condicion.length > 0) {
                            nuevo_option('#IdCondicion', { IdAnexo: 0, TipoAnexo: 2, OtroDato: '', DescAnexo: condicion }, BASE_URL + 'app/attached/autocompletado');
                        }

                        $('#direccion1').val(datos.direccion);

                        verificar_tipo_documento_identidad();
                    } else {
                        alertify.error('Número de documento inválido');
                    }
                }
            },
        });
    } else {
        alertify.error('Solo se permite ' + longitud + ' dígitos');
    }
}

function cambiar_condicion_pago() {
    var Tipo = $('#CodTV option:selected').attr('data-tipo');

    if (Tipo == 1) {
        autocompletado('#CodCondPago', { Tipo: 167, App: 'Ventas' }, BASE_URL + 'app/mantenience/payment_condition/autocompletado_');
        nuevo_option('#CodCondPago', { Tipo: 167, App: 'Ventas' }, BASE_URL + 'app/mantenience/payment_condition/autocompletado_');

        autocompletado('#FormaPago', { IdAnexo: 0, TipoAnexo: 6, OtroDato: '', App: 'Ventas' }, BASE_URL + 'app/attached/autocompletado');
        nuevo_option('#FormaPago', { IdAnexo: 0, TipoAnexo: 6, OtroDato: '', CodInterno: 1, App: 'Ventas' }, BASE_URL + 'app/attached/autocompletado');

        $('#FormaPago').removeAttr('disabled');
    } else if (Tipo == 2) {
        autocompletado('#CodCondPago', { Tipo: 168, App: 'Ventas' }, BASE_URL + 'app/mantenience/payment_condition/autocompletado_');
        nuevo_option('#CodCondPago', { Tipo: 168, App: 'Ventas' }, BASE_URL + 'app/mantenience/payment_condition/autocompletado_');

        $('#FormaPago').html('<option value="NINGUNO">NINGUNO</option>');
        $('#FormaPago').attr('disabled', true);
    }

    parametros_codTV();
}

function cambiar_codigo() {
    if ($('#Codmov').is(':focus')) {
        Codmov = Codmov.substring(5);
        $('#Codmov').val(Codmov);
    } else {
        Codmov = 'VEN' + mes + $('#Codmov').val();
        $('#Codmov').val(Codmov);
    }
}

function cambiar_fecha_contable() {
    var FecContable = $('#FecContable').val();

    $('#FecEmision').val(FecContable);
    $('#FecEmision').change();
    $('#FecVcto').val(FecContable);
}

function cambiar_estado() {
    if ($('#Estado').is(':checked')) {
        $('#Estado').val('1');
    } else {
        $('#Estado').val('0');
    }
}

function cambiar_forma_pago() {
    var CodInterno = $('#FormaPago option:selected').attr('data-codigo-interno');

    if (CodInterno != 2) $('#Banco').val('');
}

function cambiar_detraccion() {
    if ($('#Detraccion').is(':checked')) {
        $('#Detraccion').val('1');
    } else {
        $('#Detraccion').val('0');
    }
}

function cambiar_tipo_cambio_from_fecEmision() {
    var FecEmision = $('#FecEmision').val();

    $.ajax({
        url: BASE_URL + 'app/movements/sales/consulta_tipo_cambio',
        data: {
            FecEmision,
            tipo: 'consultar',
        },
        type: 'POST',
        async: false,
        success: function (data) {
            $('#ValorTC').val(data);
        },
    });
}

function cambiar_documento() {
    var CodDocumento = $('#CodDocumento option:selected').val();
    var IdSocioN = $('#IdSocioN option:selected').val();

    if (facturas.some(item => item.CodDocumento === CodDocumento)) {
        $('#NumeroDocF').attr('readonly', true);
    } else {
        $('#NumeroDocF').attr('readonly', false);
    }

    $('#Serie').val('');

    if (notas_credito.some(item => item.CodDocumento === CodDocumento)) {
        $.ajax({
            url:
                BASE_URL +
                'app/movements/sales/consulta_movimientos_nota_credito',
            data: {
                IdSocioN,
                tipo: 'nuevo',
            },
            type: 'POST',
            async: false,
            success: function (data) {
                var datos = JSON.parse(data);

                $('#tab1-tab').removeClass('disabled active');
                $('#tab2-tab').removeClass('disabled');
                $('#tab2-tab').addClass('active');

                $('#tab1').removeClass('show active');
                $('#tab2').addClass('show active');

                $('#btnReferenciaExistente').removeAttr('disabled');
                $('#btnReferenciaManual').removeAttr('disabled');
                $('#btnReferenciaManual').attr('onclick', 'seleccionar_movimiento_manual()');
                $('#btnQuitarReferencia').removeAttr('disabled');

                $('#documentoModal').modal('show');

                $('#tabla_documentos > tbody').html(datos.data);
            },
        });
    } else {
        $('#tab1-tab').removeClass('disabled');
        $('#tab1-tab').addClass('active');
        $('#tab2-tab').removeClass('active');
        $('#tab2-tab').addClass('disabled');

        $('#tab1').addClass('show active');
        $('#tab2').removeClass('show active');

        $('#btnReferenciaExistente').attr('disabled', true);
        $('#btnReferenciaManual').attr('disabled', true);
        $('#btnReferenciaManual').removeAttr('onclick');
        $('#btnQuitarReferencia').attr('disabled', true);

        $('#documentoModal').modal('hide');
        $('#tabla_documentos > tbody').html('');

        $('#tabla_referencia > tbody').html('<tr id="tr_vacio_referencia"><td align="center" colspan="10">No hay datos para mostrar</td></tr>');
    }
}

function consulta_notas_credito() {
    var IdSocioN = $('#IdSocioN option:selected').val();

    $.ajax({
        url:
            BASE_URL +
            'app/movements/sales/consulta_movimientos_nota_credito',
        data: {
            IdSocioN,
            tipo: 'nuevo',
        },
        type: 'POST',
        async: false,
        success: function (data) {
            var datos = JSON.parse(data);

            if (datos.estado) {
                $('#tabla_documentos').addClass('tabla_documentos');
            } else {
                $('#tabla_documentos').removeClass('tabla_documentos');
            }

            $('#documentoModal').modal('show');

            $('#tabla_documentos > tbody').html(datos.data);
        },
    });
}

function consulta_movimiento(IdMov) {
    $.ajax({
        url: BASE_URL + 'app/movements/sales/consulta_movimiento',
        data: {
            IdMov,
            tipo: 'nuevo',
        },
        type: 'POST',
        async: false,
        success: function (data) {
            var datos = JSON.parse(data);

            $('#movimientoModal').modal('show');
            $('#movimientoModalLabel').html(datos.titulo);

            $('#tabla_movimiento > tbody').html(datos.data);
            $('#total_movimiento').html(datos.total);
        },
    });
}

function seleccionar_movimiento_existente(IdMov) {
    $.ajax({
        url:
            BASE_URL + 'app/movements/sales/seleccionar_movimiento_existente',
        data: {
            IdMov,
            tipo: 'nuevo',
        },
        type: 'POST',
        async: false,
        success: function (data) {
            console.log(data);
            var datos = JSON.parse(data);

            $('#tr_vacio_referencia').remove();

            if (datos.estado) {
                if ($('#tr_referencia_existente_' + IdMov).length == 0) {
                    $('#tabla_referencia > tbody').append(datos.data);
                    $('#documentoModal').modal('hide');

                    set_suma_total_referencia();

                    if ($('.ValorTCReferenciaExistente').length > 0) {
                        var ValorTC = $('.ValorTCReferenciaExistente').first().val();

                        $('#ValorTC').val(ValorTC);

                        $('.ValorTC').each(function (i) {
                            $(this).val(ValorTC);
                        });

                        $('.CodMoneda').each(function (i) {
                            var CodMoneda = $(
                                '#CodMoneda' + (i + 1) + ' option:selected'
                            ).val();
                            var ValorTC = parseFloat($('#ValorTC' + (i + 1)).val());
                            var DebeSol = parseFloat($('#DebeSol' + (i + 1)).val());
                            var HaberSol = parseFloat($('#HaberSol' + (i + 1)).val());
                            var DebeDol = parseFloat($('#DebeDol' + (i + 1)).val());
                            var HaberDol = parseFloat($('#HaberDol' + (i + 1)).val());

                            if (CodMoneda == 'MO001') {
                                if (DebeDol != 0) {
                                    $('#DebeDol' + (i + 1)).val((DebeSol / ValorTC).toFixed(2));
                                } else if (HaberDol != 0) {
                                    $('#HaberDol' + (i + 1)).val((HaberSol / ValorTC).toFixed(2));
                                }
                            } else if (CodMoneda == 'MO002') {
                                if (DebeSol != 0) {
                                    $('#DebeSol' + (i + 1)).val((DebeSol * ValorTC).toFixed(2));
                                } else if (HaberSol != 0) {
                                    $('#HaberSol' + (i + 1)).val((HaberSol * ValorTC).toFixed(2));
                                }
                            }
                        });

                        set_suma_total();
                    }
                } else {
                    alertify.alert('Ya se agrego', function () { }).set({ title: 'Contabilidad' });
                }
            } else {
                $('#tabla_referencia > tbody').html(datos.data);
            }
        },
    });
}

function seleccionar_movimiento_manual() {
    var ValorTC = $('#ValorTC').val();

    IdReferenciaManual++;

    $.ajax({
        url: BASE_URL + 'app/movements/sales/seleccionar_movimiento_manual',
        data: {
            IdReferenciaManual,
            ValorTC,
            tipo: 'nuevo',
        },
        type: 'POST',
        async: false,
        success: function (data) {
            var datos = JSON.parse(data);

            $('#tr_vacio_referencia').remove();

            $('#tabla_referencia > tbody').append(datos.data);

            autocompletado('.CodDocumentoReferenciaManual', { App: 'Ventas' }, BASE_URL + 'app/documento/autocompletado');
        },
    });
}

function referencia_existente(IdMovDet) {
    var Tipo = 1;

    if (Importado == 0) {
        if (!$('#tr_referencia_existente_' + IdMovDet + ' td').hasClass('estilo-referencia')) {
            $('.tr_referencia td').removeClass('estilo-referencia');
            $('#tr_referencia_existente_' + IdMovDet + ' td').toggleClass('estilo-referencia');
            $('#btnQuitarReferencia').attr('onclick', 'quitar_referencia(' + IdMovDet + ', ' + Tipo + ')');
        } else {
            $('.tr_referencia td').removeClass('estilo-referencia');
            $('#btnQuitarReferencia').removeAttr('onclick');
        }
    }
}

function referencia_manual(IdManual) {
    var Tipo = 2;

    if (Importado == 0) {
        if (!$('#tr_referencia_manual_' + IdManual + ' td').hasClass('estilo-referencia')) {
            $('.tr_referencia td').removeClass('estilo-referencia');
            $('#tr_referencia_manual_' + IdManual + ' td').toggleClass('estilo-referencia');
            $('#btnQuitarReferencia').attr('onclick', 'quitar_referencia(' + IdManual + ', ' + Tipo + ')');
        } else {
            $('.tr_referencia td').removeClass('estilo-referencia');
            $('#btnQuitarReferencia').removeAttr('onclick');
        }
    }
}

function quitar_referencia(Id, Tipo) {
    if (Tipo == 1) {
        $('#tr_referencia_existente_' + Id).remove();
    } else if (Tipo == 2) {
        $('#tr_referencia_manual_' + Id).remove();
    }

    $('#btnQuitarReferencia').removeAttr('onclick');

    set_suma_total_referencia();

    if ($('.tr_referencia').length == 0) $('#tabla_referencia > tbody').html('<tr id="tr_vacio_referencia"><td align="center" colspan="10">No hay datos para mostrar</td></tr>');
}

function cambiar_TotalS_referencia_existente(id) {
    var ValorTC = $('#ValorTCReferenciaExistente' + id).val();
    var TotalSReferenciaExistente_old = parseFloat($('#TotalSReferenciaExistente' + id).attr('data-value'));
    var TotalDReferenciaExistente_old = parseFloat($('#TotalDReferenciaExistente' + id).attr('data-value'));
    var TotalSReferenciaExistente = parseFloat($('#TotalSReferenciaExistente' + id).val().length == 0 ? 0 : $('#TotalSReferenciaExistente' + id).val());

    if (TotalSReferenciaExistente > TotalSReferenciaExistente_old) {
        alertify.alert('No puede pasar el monto original ' + TotalSReferenciaExistente_old, function () { }).set({ title: 'Contabilidad' });

        $('#TotalSReferenciaExistente' + id).val(TotalSReferenciaExistente_old);
        $('#TotalDReferenciaExistente' + id).val(TotalDReferenciaExistente_old);
    } else {
        var total = TotalSReferenciaExistente / ValorTC;
        $('#TotalDReferenciaExistente' + id).val(total.toFixed(2));
    }

    set_suma_total_referencia();
}

function cambiar_TotalD_referencia_existente(id) {
    var ValorTC = $('#ValorTCReferenciaExistente' + id).val();
    var TotalSReferenciaExistente_old = parseFloat($('#TotalSReferenciaExistente' + id).attr('data-value'));
    var TotalDReferenciaExistente_old = parseFloat($('#TotalDReferenciaExistente' + id).attr('data-value'));
    var TotalDReferenciaExistente = parseFloat($('#TotalDReferenciaExistente' + id).val().length == 0 ? 0 : $('#TotalDReferenciaExistente' + id).val());

    if (TotalDReferenciaExistente > TotalDReferenciaExistente_old) {
        alertify.alert('No puede pasar el monto original ' + TotalDReferenciaExistente_old, function () { }).set({ title: 'Contabilidad' });

        $('#TotalSReferenciaExistente' + id).val(TotalSReferenciaExistente_old);
        $('#TotalDReferenciaExistente' + id).val(TotalDReferenciaExistente_old);
    } else {
        var total = TotalDReferenciaExistente * ValorTC;
        $('#TotalSReferenciaExistente' + id).val(total.toFixed(2));
    }

    set_suma_total_referencia();
}

function cambiar_TotalS_referencia_manual(id) {
    var ValorTC = $('#ValorTCReferenciaManual' + id).val();
    var TotalSReferenciaManual = parseFloat($('#TotalSReferenciaManual' + id).val().length == 0 ? 0 : $('#TotalSReferenciaManual' + id).val());
    var Total = TotalSReferenciaManual == 0 ? 0 : TotalSReferenciaManual / ValorTC;

    $('#TotalDReferenciaManual' + id).val(Total.toFixed(2));

    set_suma_total_referencia();
}

function cambiar_TotalD_referencia_manual(id) {
    var ValorTC = $('#ValorTCReferenciaManual' + id).val();
    var TotalDReferenciaManual = parseFloat($('#TotalDReferenciaManual' + id).val().length == 0 ? 0 : $('#TotalDReferenciaManual' + id).val());
    var Total = TotalDReferenciaManual * ValorTC;

    $('#TotalSReferenciaManual' + id).val(Total.toFixed(2));

    set_suma_total_referencia();
}

function set_suma_total_referencia() {
    var CodMoneda = $('.CodMoneda').length == 0 ? $('#CodMoneda').val() : $('.CodMoneda').first().val();
    var total = 0;
    const exp = /(\d)(?=(\d{3})+(?!\d))/g;
    const rep = '$1,';

    if (CodMoneda == 'MO001') {
        $('.referencia_TotalS').each(function (i) {
            total += parseFloat($(this).val().length == 0 ? 0 : $(this).val());
        });
    } else if (CodMoneda == 'MO002') {
        $('.referencia_TotalD').each(function (i) {
            total += parseFloat($(this).val().length == 0 ? 0 : $(this).val());
        });
    }

    $('#referencia_Total').val(total.toFixed(2).toString().replace(exp, rep));
}

function verificar_serie() {
    var Serie = $('#Serie').val();

    $('#Serie').val(Serie.toUpperCase());

    var Serie = $('#Serie').val();
    var es_numero = $('#CodDocumento option:selected').attr('data-es-numero');
    var serie = $('#CodDocumento option:selected').attr('data-serie') ? $('#CodDocumento option:selected').attr('data-serie').split(',') : '';
    var longitud = $('#CodDocumento option:selected').attr('data-longitud') ? $('#CodDocumento option:selected').attr('data-longitud') : 4;

    var estado_serie = false;

    if (Serie.length > longitud) {
        alertify.alert('La serie debe tener una longitud de ' + longitud + ' caracteres', function () { }).set({ title: 'Contabilidad' });

        $('#Serie').val(Serie.substring(0, 4));
    }

    if (serie.length > 0) {
        if (Serie.length == longitud && Serie.match(/[a-zA-Z]/g)) {
            for (let index = 0; index < serie.length; index++) {
                if (
                    JSON.stringify(serie[index].toUpperCase().split(/(\d)/).filter((e) => isNaN(e))) ==
                    JSON.stringify(Serie.toUpperCase().split(/(\d)/).filter((e) => isNaN(e)))
                ) {
                    estado_serie = false;
                    index = serie.length;
                } else {
                    estado_serie = true;
                }
            }

            if (estado_serie) {
                $('#Serie').val('');

                let mensaje = 'La serie debe emprezar con ' + serie.join(',');

                mensaje += es_numero == 'si' ? ' (y el resto alfanúmerico)' : '';

                alertify.alert(mensaje, function () { }).set({ title: 'Contabilidad' });
            }
        }
    }
}

function verificar_serie_from_table(id) {
    var Serie = $('#SerieDoc' + id).val();

    $('#SerieDoc' + id).val(Serie.toUpperCase());

    var Serie = $('#SerieDoc' + id).val();
    var es_numero = $('#CodDocumento' + id + ' option:selected').attr('data-es-numero');
    var serie = $('#CodDocumento' + id + ' option:selected').attr('data-serie') ? $('#CodDocumento' + id + ' option:selected').attr('data-serie').split(',') : '';
    var longitud = $('#CodDocumento' + id + ' option:selected').attr('data-longitud') ? $('#CodDocumento' + id + ' option:selected').attr('data-longitud') : 4;

    var estado_serie = false;

    if (Serie.length > longitud) {
        alertify.alert('La serie debe tener una longitud de ' + longitud + ' caracteres', function () { }).set({ title: 'Contabilidad' });

        $('#SerieDoc' + id).val(Serie.substring(0, 4));
    }

    if (serie.length > 0) {
        if (Serie.length == longitud && Serie.match(/[a-zA-Z]/g)) {
            for (let index = 0; index < serie.length; index++) {
                if (
                    JSON.stringify(serie[index].toUpperCase().split(/(\d)/).filter((e) => isNaN(e))) ==
                    JSON.stringify(Serie.toUpperCase().split(/(\d)/).filter((e) => isNaN(e)))
                ) {
                    estado_serie = false;
                    index = serie.length;
                } else {
                    estado_serie = true;
                }
            }

            if (estado_serie) {
                $('#SerieDoc' + id).val('');

                let mensaje = 'La serie debe emprezar con ' + serie.join(',');

                mensaje += es_numero == 'si' ? ' (y el resto alfanúmerico)' : '';

                alertify.alert(mensaje, function () { }).set({ title: 'Contabilidad' });
            } else {
                $('.SerieDoc').each(function (i) {
                    $(this).val(Serie);
                });
            }
        }
    }
}

function cambiar_serie() {
    var Serie = $('#Serie').val().length > 0 ? $('#Serie').val() : '1';

    if (Serie.length > 0) {
        switch (Serie.length) {
            case 1:
                $('#Serie').val(
                    Serie.match(/[a-zA-Z]/g) ? Serie + '000' : '000' + Serie
                );
                break;
            case 2:
                $('#Serie').val(Serie.match(/[a-zA-Z]/g) ? Serie + '00' : '00' + Serie);
                break;
            case 3:
                $('#Serie').val(Serie.match(/[a-zA-Z]/g) ? Serie + '0' : '0' + Serie);
                break;
            case 4:
                $('#Serie').val(Serie);
                break;
            default:
                $('#Serie').val(Serie);
                break;
        }
    }
}

function cambiar_serie_from_table(id) {
    var Serie = $('#SerieDoc' + id).val();

    if (Serie.length > 0) {
        switch (Serie.length) {
            case 1:
                Serie = Serie.match(/[a-zA-Z]/g) ? Serie + '000' : '000' + Serie;
                break;
            case 2:
                Serie = Serie.match(/[a-zA-Z]/g) ? Serie + '00' : '00' + Serie;
                break;
            case 3:
                Serie = Serie.match(/[a-zA-Z]/g) ? Serie + '0' : '0' + Serie;
                break;
            case 4:
                Serie = Serie;
                break;
            default:
                Serie = Serie;
                break;
        }

        $('.SerieDoc').each(function (i) {
            $(this).val(Serie);
        });
    }
}

function cambiar_igv() {
    var TasaIGV =
        $('#TasaIGV').val().length == 0 ? 0 : parseInt($('#TasaIGV').val());

    if (TasaIGV != 18) {
        alertify.alert('Ingrese la tasa de igv correcta 18%', function () { }).set({ title: 'Contabilidad' });
    }
}

function set_total(id) {
    var Afecto = $('#Afecto').val().length == 0 ? 0 : parseFloat($('#Afecto').val());
    var Inafecto = $('#Inafecto').val().length == 0 ? 0 : parseFloat($('#Inafecto').val());
    var Exonerado = $('#Exonerado').val().length == 0 ? 0 : parseFloat($('#Exonerado').val());
    var Descuento = $('#Descuento').val().length == 0 ? 0 : parseFloat($('#Descuento').val());
    var Anticipo = $('#Anticipo').val().length == 0 ? 0 : parseFloat($('#Anticipo').val());
    var ISC = $('#ISC').val().length == 0 ? 0 : parseFloat($('#ISC').val());
    var Igv = $('#Igv').val().length == 0 ? 0 : parseFloat($('#Igv').val());
    var Total = $('#Total').val().length == 0 ? 0 : parseFloat($('#Total').val());

    if (id == 'Afecto') {
        id_set_total = 'Afecto';
        afecto_tmp = Afecto;
        anticipo_tmp = Anticipo != 0 ? Anticipo * 0.18 : 0;
        isc_tmp = ISC * 0.18;
        igv_tmp = 0.18 * Afecto + isc_tmp - anticipo_tmp;
        total_tmp = Afecto + igv_tmp + Inafecto + Exonerado - Descuento + ISC - Anticipo;
    }

    if (id == 'Inafecto') {
        anticipo_tmp = Anticipo != 0 ? Anticipo * 0.18 : 0;
        isc_tmp = ISC * 0.18;
        igv_tmp = id_set_total == 'Afecto' ? 0.18 * Afecto + isc_tmp - anticipo_tmp - 0.18 * Descuento : igv + isc_tmp - anticipo_tmp - 0.18 * Descuento;
        total_tmp = afecto_tmp + Igv + Inafecto + Exonerado - Descuento + ISC - Anticipo;
    }

    if (id == 'Exonerado') {
        anticipo_tmp = Anticipo != 0 ? Anticipo * 0.18 : 0;
        isc_tmp = ISC * 0.18;
        igv_tmp = id_set_total == 'Afecto' ? 0.18 * Afecto + isc_tmp - anticipo_tmp - 0.18 * Descuento : igv + isc_tmp - anticipo_tmp - 0.18 * Descuento;
        total_tmp = afecto_tmp + Igv + Inafecto + Exonerado - Descuento + ISC - Anticipo;
    }

    if (id == 'Descuento') {
        anticipo_tmp = Anticipo != 0 ? Anticipo * 0.18 : 0;
        isc_tmp = ISC * 0.18;
        descuento_tmp = Anticipo != 0 ? 0.18 * Descuento : 0;
        igv_tmp = id_set_total == 'Afecto' ? 0.18 * Afecto + isc_tmp - anticipo_tmp - descuento_tmp : igv + isc_tmp - anticipo_tmp - descuento_tmp;
        total_tmp = Afecto + Inafecto + Exonerado - Descuento + igv_tmp - Anticipo + ISC;
    }

    if (id == 'Anticipo') {
        $('#ISC').val(0);

        anticipo_tmp = Anticipo != 0 ? Anticipo * 0.18 : 0;
        igv_tmp = id_set_total == 'Afecto' ? 0.18 * Afecto - anticipo_tmp - 0.18 * Descuento : igv - anticipo_tmp - 0.18 * Descuento;
        total_tmp = Afecto + Inafecto + Exonerado - Descuento + igv_tmp - Anticipo;
    }

    if (id == 'ISC') {
        $('#Anticipo').val(0);

        anticipo_tmp = Anticipo != 0 ? Anticipo * 0.18 : 0;
        isc_tmp = ISC * 0.18;
        igv_tmp = id_set_total == 'Afecto' ? 0.18 * Afecto + isc_tmp : igv + isc_tmp;
        total_tmp = Afecto + Inafecto + Exonerado - Descuento + igv_tmp - Anticipo + ISC;
    }

    if (id == 'Igv') {
        id_set_total = 'Afecto';
        afecto_tmp = Igv / 0.18;
        anticipo_tmp = Anticipo != 0 ? Anticipo * 0.18 : 0;
        isc_tmp = ISC * 0.18;
        igv_tmp = id_set_total == 'Afecto' ? afecto_tmp + isc_tmp - anticipo_tmp - 0.18 * Descuento : igv + isc_tmp - anticipo_tmp - 0.18 * Descuento;
        total_tmp = igv_tmp + Igv + Inafecto + Exonerado - Descuento + ISC - Anticipo;
    }

    if (id == 'Total') {
        id_set_total = 'Total';
        afecto_tmp = Total / 1.18;
        igv = Total - afecto_tmp;
        igv_tmp = Total - afecto_tmp;
        total_tmp = Total;

        $('#Afecto').val(afecto_tmp.toFixed(2));
        $('#Igv').val(igv_tmp.toFixed(2));
    } else if (id == 'Igv') {
        $('#Afecto').val(afecto_tmp.toFixed(2));
        $('#Total').val(total_tmp.toFixed(2));
    } else {
        $('#Igv').val(igv_tmp.toFixed(2));
        $('#Total').val(total_tmp.toFixed(2));
    }
}

function registrar_socio_negocio(event) {
    var numero_documento = $('.select2-search__field').val();
    var datos;
    var existe = true;
    var error = false;

    if (numero_documento.length == 8 || numero_documento.length == 11) {
        $.ajax({
            url: BASE_URL + 'app/movements/sales/consulta_sunat',
            data: {
                numero_documento,
                tipo: 'verificar',
            },
            type: 'POST',
            async: false,
            success: function (data) {
                var datos = JSON.parse(data);

                existe = datos.existe;
            },
        });
    } else {
        alertify.error('Solo se permite 8 o 11 dígitos');
    }

    if (!existe) {
        alertify.confirm('Desea consultar en SUNAT', function (e) {
            if (e) {
                setTimeout(() => {
                    $.ajax({
                        url: BASE_URL + 'app/movements/sales/consulta_sunat',
                        data: {
                            numero_documento,
                            tipo: 'consultar',
                        },
                        type: 'POST',
                        async: false,
                        success: function (data) {
                            if (data == 'null') {
                                error = true;
                            } else {
                                datos = JSON.parse(data);

                                if (datos.error) error = true;
                            }
                        },
                    });

                    if (!error) {
                        alertify.confirm('Desea registrar el Cliente: ' + datos.nombre.toUpperCase() + (datos.condicion.length > 0 ? '<br>Condición: ' + datos.condicion.toUpperCase() : ''),
                            function (e) {
                                if (e) {
                                    $.ajax({
                                        url:
                                            BASE_URL + 'app/movements/sales/consulta_sunat',
                                        data: {
                                            datos,
                                            tipo: 'registrar',
                                        },
                                        type: 'POST',
                                        async: false,
                                        success: function (data) {
                                            var datos = JSON.parse(data);

                                            if (datos.estado) {
                                                $('#IdSocioN').html(datos.option).trigger('change');

                                                alertify.success('Cliente registrado correctamente!');
                                            } else {
                                                alertify.error('Error. Consulte con el administrador');
                                            }
                                        },
                                    });
                                }
                            }
                        ).set({ title: 'Contabilidad' }).set('labels', { ok: 'Si', cancel: 'No', });
                    } else {
                        alertify.error('Número documento inválido');
                    }
                }, 1);
            }
        }).set({ title: 'Contabilidad' }).set('labels', { ok: 'Si', cancel: 'No' });
    } else {
        alertify.error('Número documento ya existe');
    }
}

var IdSocioN_auxiliar = 0;
var TipoOperacion_auxiliar = 0;
var CodCondPago_auxiliar = 0;

function cambiar_cuenta(id) {
    var CodCuenta = $('#CodCuenta' + id + ' option:selected').val();
    var IdSocioN = $('#IdSocioN' + id).is('select') ? $('#IdSocioN' + id + ' option:selected').val() : 0;
    var TipoOperacion = $('#TipoOperacion' + id).is('select') ? $('#TipoOperacion' + id + ' option:selected').val() : 0;
    var CodCondPago = $('#CodCondPago' + id).is('select') ? $('#CodCondPago' + id + ' option:selected').val() : 0;

    if (IdSocioN != 0) IdSocioN_auxiliar = IdSocioN;

    if (TipoOperacion != 0) TipoOperacion_auxiliar = TipoOperacion;

    if (CodCondPago != 0) CodCondPago_auxiliar = CodCondPago;

    $.ajax({
        url: BASE_URL + 'app/movements/sales/consulta_codigo_cuenta',
        data: {
            id,
            CodCuenta,
            IdSocioN: IdSocioN_auxiliar,
            TipoOperacion: TipoOperacion_auxiliar,
            CodCondPago: CodCondPago_auxiliar,
            tipo: 'nuevo',
        },
        type: 'POST',
        async: false,
        success: function (data) {
            var datos = JSON.parse(data);

            if (datos.estado) {
                $('#td_ctacte_' + id).html(datos.ctacte);

                $('#td_socio_negocio_' + id).html(datos.socio_negocio);

                autocompletado('select.IdSocioN', { verDocumento: 'true', App: 'Ventas' }, BASE_URL + 'app/mantenience/business_partner/autocompletado_');

                $('#td_tipo_operacion_' + id).html(datos.tipo_operacion);

                autocompletado('select.TipoOperacion', { IdAnexo: 0, TipoAnexo: 5, OtroDato: '', App: 'Ventas' }, BASE_URL + 'app/attached/autocompletado');

                $('#td_condicion_pago_' + id).html(datos.condicion_pago);

                autocompletado('select.CodCondPago', { App: 'Ventas' }, BASE_URL + 'app/mantenience/payment_condition/autocompletado_');

                $('#td_documento_retencion_' + id).html(datos.documento_retencion);

                $('#td_centro_costo_' + id).html(datos.centro_costo);

                autocompletado('select.CodCcosto', { App: 'Ventas' }, BASE_URL + 'app/mantenience/cost_center/autocompletado');

                $('#td_activo_fijo_' + id).html(datos.activo_fijo);

                autocompletado('select.IdActivo', { App: 'Ventas' }, BASE_URL + 'app/mantenience/fixed_assets/autocompletado');
            } else {
                alertify.error(datos.mensaje);
            }
        },
    });

    var CtaCte = parseInt($('#CtaCte' + id).val());
    var Parametro = $('#Parametro' + id + ' option:selected').val();

    $('#tr_' + id + ' td').removeClass('background-total');
    $('#tr_' + id + ' td').removeClass('background-ctacte');

    if (Referencia == 1) {
        if (CtaCte == 1) {
            if (Parametro == "TOTAL") {
                $("#tr_" + id + ' td').addClass("background-total");

                $("#CodCuenta" + id).attr("disabled", true);
                $("#CodMoneda" + id).attr("disabled", true);
                $("#DebeSol" + id).attr("readonly", true);
                $("#HaberSol" + id).attr("readonly", true);
                $("#DebeDol" + id).attr("readonly", true);
                $("#HaberDol" + id).attr("readonly", true);
                $("#FecEmision" + id).attr("readonly", true);
                $("#FecVcto" + id).attr("readonly", true);
                $("#IdSocioN" + id).attr("disabled", true);
                $("#CodDocumento" + id).attr("disabled", true);
                $("#SerieDoc" + id).attr("readonly", true);
                $("#NumeroDoc" + id).attr("readonly", true);
                $("#NumeroDocF" + id).attr("readonly", true);
                $("#TipoOperacion" + id).attr("disabled", true);
                $("#CodCcosto" + id).attr("disabled", true);
                $("#CodCondPago" + id).attr("disabled", true);
                $("#DocRetencion" + id).attr("readonly", true);
                $("#DocDetraccion" + id).attr("readonly", true);
                $("#Parametro" + id).attr("disabled", true);
                $("#IdDetraccion" + id).attr("disabled", true);
                $("#IdTipOpeDetra" + id).attr("disabled", true);
                $("#IdenContProy" + id).attr("readonly", true);
            } else {
                $("#tr_" + id + ' td').addClass("background-ctacte");

                $("#CodCuenta" + id).removeAttr("disabled");
                $("#CodMoneda" + id).removeAttr("disabled");
                $("#DebeSol" + id).removeAttr("readonly");
                $("#HaberSol" + id).removeAttr("readonly");
                $("#DebeDol" + id).removeAttr("readonly");
                $("#HaberDol" + id).removeAttr("readonly");
                $("#FecEmision" + id).removeAttr("readonly");
                $("#FecVcto" + id).removeAttr("readonly");
                $("#CodDocumento" + id).removeAttr("disabled");
                $("#SerieDoc" + id).removeAttr("readonly");
                $("#NumeroDoc" + id).removeAttr("readonly");
                $("#NumeroDocF" + id).removeAttr("readonly");
                $("#DocDetraccion" + id).removeAttr("readonly");
                $("#Parametro" + id).removeAttr("disabled");
                $("#IdDetraccion" + id).removeAttr("disabled");
                $("#IdTipOpeDetra" + id).removeAttr("disabled");
                $("#IdenContProy" + id).removeAttr("readonly");
            }
        }
    } else {
        if (CtaCte == 1) {
            if (Parametro == "TOTAL") {
                $("#tr_" + id + ' td').addClass("background-total");
            } else {
                $("#tr_" + id + ' td').addClass("background-ctacte");
            }
        }
    }
}

function cambiar_cuenta_contable_banco() {
    var DescCuentaBanco = $('#CodCuentaBanco option:selected').attr('data-descripcion');

    $('#DescCuentaBanco').val(DescCuentaBanco);
}

function cambiar_moneda_principal() {
    set_suma_total_referencia();
}

function cambiar_moneda(id) {
    var CodMoneda = $('#CodMoneda' + id + ' option:selected').val();
    var texto = $('#CodMoneda' + id + ' option:selected').text();

    $('.CodMoneda').each(function (i) {
        var option = new Option(texto, CodMoneda, true, true);

        $(this).html(option);
    });

    $('.CodMoneda').each(function (i) {
        var CodMoneda = $('#CodMoneda' + (i + 1) + ' option:selected').val();
        var ValorTC = parseFloat($('#ValorTC' + (i + 1)).val());
        var DebeSol = parseFloat($('#DebeSol' + (i + 1)).val());
        var HaberSol = parseFloat($('#HaberSol' + (i + 1)).val());
        var DebeDol = parseFloat($('#DebeDol' + (i + 1)).val());
        var HaberDol = parseFloat($('#HaberDol' + (i + 1)).val());

        if (CodMoneda == 'MO001') {
            if (DebeSol != 0) {
                $('#DebeSol' + (i + 1)).val(DebeDol.toFixed(2));
            } else if (HaberSol != 0) {
                $('#HaberSol' + (i + 1)).val(HaberDol.toFixed(2));
            }

            if (DebeDol != 0) {
                $('#DebeDol' + (i + 1)).val(DebeSol.toFixed(2));
            } else if (HaberDol != 0) {
                $('#HaberDol' + (i + 1)).val(HaberSol.toFixed(2));
            }
        } else if (CodMoneda == 'MO002') {
            if (DebeSol != 0) {
                $('#DebeSol' + (i + 1)).val(DebeDol.toFixed(2));
            } else if (HaberSol != 0) {
                $('#HaberSol' + (i + 1)).val(HaberDol.toFixed(2));
            }

            if (DebeDol != 0) {
                $('#DebeDol' + (i + 1)).val(DebeSol.toFixed(2));
            } else if (HaberDol != 0) {
                $('#HaberDol' + (i + 1)).val(HaberSol.toFixed(2));
            }
        }
    });

    set_suma_total();
    set_suma_total_referencia();
}

function cambiar_tipo_cambio_from_table(id) {
    var ValorTC = $('#ValorTC' + id).val();

    $('.ValorTC').each(function (i) {
        $(this).val(ValorTC);
    });
}

function cambiar_fecha_emision(id) {
    var FecEmision = $('#FecEmision' + id).val();
    var FecVcto = $('#FecVcto' + id).val();

    if (Date.parse(formato_fecha(FecEmision)) <= Date.parse(formato_fecha(FecVcto))) {
        $('.FecEmision').each(function (i) {
            $(this).val(FecEmision);
            $(this).attr('data-value', FecEmision);
        });

        $.ajax({
            url: BASE_URL + 'app/movements/sales/consulta_tipo_cambio',
            data: {
                FecEmision,
                tipo: 'consultar_database',
            },
            type: 'POST',
            async: false,
            success: function (data) {
                $('.ValorTC').each(function (i) {
                    $(this).val(data);
                });

                $('.CodMoneda').each(function (i) {
                    var CodMoneda = $('#CodMoneda' + (i + 1) + ' option:selected').val();
                    var ValorTC = parseFloat($('#ValorTC' + (i + 1)).val());
                    var DebeSol = parseFloat($('#DebeSol' + (i + 1)).val());
                    var HaberSol = parseFloat($('#HaberSol' + (i + 1)).val());
                    var DebeDol = parseFloat($('#DebeDol' + (i + 1)).val());
                    var HaberDol = parseFloat($('#HaberDol' + (i + 1)).val());

                    if (CodMoneda == 'MO001') {
                        if (DebeSol != 0) {
                            $('#DebeDol' + (i + 1)).val((DebeSol / ValorTC).toFixed(2));
                        } else if (HaberSol != 0) {
                            $('#HaberDol' + (i + 1)).val((HaberSol / ValorTC).toFixed(2));
                        }
                    } else if (CodMoneda == 'MO002') {
                        if (DebeDol != 0) {
                            $('#DebeSol' + (i + 1)).val((DebeDol * ValorTC).toFixed(2));
                        } else if (HaberDol != 0) {
                            $('#HaberSol' + (i + 1)).val((HaberDol * ValorTC).toFixed(2));
                        }
                    }
                });
            },
        });
    } else {
        $('#FecEmision' + id).datepicker('setDate', $('#FecEmision' + id).attr('data-value'));

        alertify.alert('La Fecha de Emisión no puede ser mayor que la Fecha de Vencimiento', function () { }).set({ title: 'Contabilidad' });
    }
}

function cambiar_comprobante(id) {
    var CodDocumento = $('#CodDocumento' + id + ' option:selected').val();
    var texto = $('#CodDocumento' + id + ' option:selected').text();
    var es_numero = $('#CodDocumento' + id + ' option:selected').attr('data-es-numero');
    var serie = $('#CodDocumento' + id + ' option:selected').attr('data-serie');
    var longitud = $('#CodDocumento' + id + ' option:selected').attr('data-longitud');

    $('.CodDocumento').each(function (i) {
        var option = new Option(texto, CodDocumento, true, true);
        option.setAttribute('data-es-numero', es_numero);
        option.setAttribute('data-serie', serie);
        option.setAttribute('data-longitud', longitud);

        $(this).html(option);

        if (facturas.some(item => item.CodDocumento === CodDocumento)) {
            $('#NumeroDocF' + (i + 1)).val('');
            $('#NumeroDocF' + (i + 1)).attr('readonly', true);
        } else {
            $('#NumeroDocF' + (i + 1)).attr('readonly', false);
        }
    });
}

function cambiar_fecha_vencimiento(id) {
    var FecEmision = $('#FecEmision' + id).val();
    var FecVcto = $('#FecVcto' + id).val();

    if (Date.parse(formato_fecha(FecVcto)) >= Date.parse(formato_fecha(FecEmision))) {
        $('.FecVcto').each(function (i) {
            $(this).val(FecVcto);
            $(this).attr('data-value', FecVcto);
        });
    } else {
        $('#FecVcto' + id).datepicker('setDate', $('#FecVcto' + id).attr('data-value'));

        alertify.alert('La Fecha de Vencimiento no puede ser menor que la Fecha de Emisión', function () { }).set({ title: 'Contabilidad' });
    }
}

function cambiar_numero_inicial(id) {
    var NumeroDoc = $('#NumeroDoc' + id).val();

    $('.NumeroDoc').each(function (i) {
        $(this).val(NumeroDoc);
    });
}

function cambiar_parametro(id) {
    var CtaCte = parseInt($('#CtaCte' + id).val());
    var Parametro = $('#Parametro' + id + ' option:selected').val();

    $('#tr_' + id + ' td').removeClass('background-total');
    $('#tr_' + id + ' td').removeClass('background-ctacte');

    if (Referencia == 1) {
        if (CtaCte == 1) {
            if (Parametro == "TOTAL") {
                $("#tr_" + id + ' td').addClass("background-total");

                $("#CodCuenta" + id).attr("disabled", true);
                $("#CodMoneda" + id).attr("disabled", true);
                $("#DebeSol" + id).attr("readonly", true);
                $("#HaberSol" + id).attr("readonly", true);
                $("#DebeDol" + id).attr("readonly", true);
                $("#HaberDol" + id).attr("readonly", true);
                $("#FecEmision" + id).attr("readonly", true);
                $("#FecVcto" + id).attr("readonly", true);
                $("#IdSocioN" + id).attr("disabled", true);
                $("#CodDocumento" + id).attr("disabled", true);
                $("#SerieDoc" + id).attr("readonly", true);
                $("#NumeroDoc" + id).attr("readonly", true);
                $("#NumeroDocF" + id).attr("readonly", true);
                $("#TipoOperacion" + id).attr("disabled", true);
                $("#CodCcosto" + id).attr("disabled", true);
                $("#CodCondPago" + id).attr("disabled", true);
                $("#DocRetencion" + id).attr("readonly", true);
                $("#DocDetraccion" + id).attr("readonly", true);
                $("#Parametro" + id).attr("disabled", true);
                $("#IdDetraccion" + id).attr("disabled", true);
                $("#IdTipOpeDetra" + id).attr("disabled", true);
                $("#IdenContProy" + id).attr("readonly", true);
            } else {
                $("#tr_" + id + ' td').addClass("background-ctacte");

                $("#CodCuenta" + id).removeAttr("disabled");
                $("#CodMoneda" + id).removeAttr("disabled");
                $("#DebeSol" + id).removeAttr("readonly");
                $("#HaberSol" + id).removeAttr("readonly");
                $("#DebeDol" + id).removeAttr("readonly");
                $("#HaberDol" + id).removeAttr("readonly");
                $("#FecEmision" + id).removeAttr("readonly");
                $("#FecVcto" + id).removeAttr("readonly");
                $("#CodDocumento" + id).removeAttr("disabled");
                $("#SerieDoc" + id).removeAttr("readonly");
                $("#NumeroDoc" + id).removeAttr("readonly");
                $("#NumeroDocF" + id).removeAttr("readonly");
                $("#DocDetraccion" + id).removeAttr("readonly");
                $("#Parametro" + id).removeAttr("disabled");
                $("#IdDetraccion" + id).removeAttr("disabled");
                $("#IdTipOpeDetra" + id).removeAttr("disabled");
                $("#IdenContProy" + id).removeAttr("readonly");
            }
        }
    } else {
        if (CtaCte == 1) {
            if (Parametro == "TOTAL") {
                $("#tr_" + id + ' td').addClass("background-total");
            } else {
                $("#tr_" + id + ' td').addClass("background-ctacte");
            }
        }
    }
}

function cambiar_debe_soles(id) {
    var CodMoneda = $('.CodMoneda').length == 0 ? $('#CodMoneda').val() : $('.CodMoneda').first().val();
    var ValorTC = parseFloat($('#ValorTC' + id).val());

    var DebeSol = parseFloat($('#DebeSol' + id).val());

    $('#HaberSol' + id).val(0);
    $('#HaberDol' + id).val(0);

    if (CodMoneda == 'MO001') {
        $('#DebeDol' + id).val((DebeSol / ValorTC).toFixed(2));
    }

    set_suma_total();
}

function cambiar_debe_soles_keydown(evt, id) {
    var key = evt.which;

    if (key == 13) {
        cambiar_debe_soles(id);
    }
}

function cambiar_haber_soles(id) {
    var CodMoneda = $('.CodMoneda').length == 0 ? $('#CodMoneda').val() : $('.CodMoneda').first().val();
    var ValorTC = parseFloat($('#ValorTC' + id).val());
    var HaberSol = parseFloat($('#HaberSol' + id).val());

    $('#DebeSol' + id).val(0);
    $('#DebeDol' + id).val(0);

    if (CodMoneda == 'MO001') {
        $('#HaberDol' + id).val((HaberSol / ValorTC).toFixed(2));
    }

    set_suma_total();
}

function cambiar_haber_soles_keydown(evt, id) {
    var key = evt.which;

    if (key == 13) {
        cambiar_haber_soles(id);
    }
}

function cambiar_debe_dolar(id) {
    var CodMoneda = $('.CodMoneda').length == 0 ? $('#CodMoneda').val() : $('.CodMoneda').first().val();
    var ValorTC = parseFloat($('#ValorTC' + id).val());
    var DebeDol = parseFloat($('#DebeDol' + id).val());

    $('#HaberDol' + id).val(0);
    $('#HaberSol' + id).val(0);

    if (CodMoneda == 'MO002') {
        $('#DebeSol' + id).val((DebeDol * ValorTC).toFixed(2));
    }

    set_suma_total();
}

function cambiar_debe_dolar_keydown(evt, id) {
    var key = evt.which;

    if (key == 13) {
        cambiar_debe_dolar(id);
    }
}

function cambiar_haber_dolar(id) {
    var CodMoneda = $('.CodMoneda').length == 0 ? $('#CodMoneda').val() : $('.CodMoneda').first().val();
    var ValorTC = parseFloat($('#ValorTC' + id).val());
    var HaberDol = parseFloat($('#HaberDol' + id).val());

    $('#DebeDol' + id).val(0);
    $('#DebeSol' + id).val(0);

    if (CodMoneda == 'MO002') {
        $('#HaberSol' + id).val((HaberDol * ValorTC).toFixed(2));
    }

    set_suma_total();
}

function cambiar_haber_dolar_keydown(evt, id) {
    var key = evt.which;

    if (key == 13) {
        cambiar_haber_dolar(id);
    }
}

function set_suma_total() {
    var total_DebeSol = 0;
    var total_HaberSol = 0;
    var total_DebeDol = 0;
    var total_HaberDol = 0;

    $('.DebeSol').each(function (i) {
        total_DebeSol += parseFloat($(this).val());
    });

    $('.HaberSol').each(function (i) {
        total_HaberSol += parseFloat($(this).val());
    });

    $('.DebeDol').each(function (i) {
        total_DebeDol += parseFloat($(this).val());
    });

    $('.HaberDol').each(function (i) {
        total_HaberDol += parseFloat($(this).val());
    });

    $('#total_DebeSol').val(total_DebeSol.toFixed(2));
    $('#total_HaberSol').val(total_HaberSol.toFixed(2));
    $('#total_DebeDol').val(total_DebeDol.toFixed(2));
    $('#total_HaberDol').val(total_HaberDol.toFixed(2));
}

var estado_grabar = false;

function agregar() {
    var CodTV = $('#CodTV option:selected').val();
    var CodMoneda = $('#CodMoneda option:selected').val();
    var ValorTC = $('#ValorTC').val();
    var FecEmision = $('#FecEmision').val();
    var FecVcto = $('#FecVcto').val();
    var IdSocioN = $('#IdSocioN option:selected').val();
    var CodDocumento = $('#CodDocumento option:selected').val();
    var Serie = $('#Serie').val();
    var NumeroDoc = $('#NumeroDoc').val();
    var NumeroDocF = $('#NumeroDocF').val();
    var TipoOperacion = $('#TipoOperacion option:selected').val();
    var CodCondPago = $('#CodCondPago option:selected').val();

    var Afecto = parseFloat($('#Afecto').val());
    var Inafecto = parseFloat($('#Inafecto').val());
    var Exonerado = parseFloat($('#Exonerado').val());
    var Descuento = parseFloat($('#Descuento').val());
    var Anticipo = parseFloat($('#Anticipo').val());
    var ISC = parseFloat($('#ISC').val());
    var Igv = parseFloat($('#Igv').val());
    var Total = parseFloat($('#Total').val());

    var longitud = $('#CodDocumento option:selected').attr('data-longitud') ? $('#CodDocumento option:selected').attr('data-longitud') : 4;
    var TasaIGV = $('#TasaIGV').val().length == 0 ? 0 : parseFloat($('#TasaIGV').val());
    var datos;

    var referencia_Total = parseFloat($('#referencia_Total').val().replace(',', ''));

    if (notas_credito.some(item => item.CodDocumento === CodDocumento)) {
        if (referencia_Total != 0 && Total > referencia_Total) {
            alertify.alert('El Total No Puede Ser Mayor a ' + referencia_Total, function () { }).set({ title: 'Contabilidad' });

            return false;
        }
    }

    if (IdSocioN == null) {
        $('#IdSocioN').focus();

        alertify.alert('Ingrese un Cliente Correcto', function () { }).set({ title: 'Contabilidad' });

        return false;
    }

    if (Serie.length == 0) {
        alertify.alert('Ingrese la Serie del Documento', function () { }).set({ title: 'Contabilidad' });

        return false;
    }

    if (Serie.length > longitud || Serie.length < longitud) {
        alertify.alert('La serie debe tener una longitud de ' + longitud + ' caracteres', function () { }).set({ title: 'Contabilidad' });

        return false;
    }

    if (NumeroDoc.length == 0) {
        alertify.alert('Ingrese el Número del Documento', function () { }).set({ title: 'Contabilidad' });

        return false;
    }

    $.ajax({
        url: BASE_URL + 'app/movements/sales/consulta_codigo',
        data: {
            CodDocumento,
            Serie,
            NumeroDoc,
            tipo: 'nuevo',
            subtipo: 'documento',
        },
        type: 'POST',
        async: false,
        success: function (data) {
            datos = JSON.parse(data);
        },
    });

    if (datos && datos.existe) {
        alertify.alert('Este Documento ya se encuentra Registrado<br>Periodo: ' + datos.periodo + '<br>Mes: ' + datos.mes + '<br>Movi: ' + datos.movi, function () { }).set({ title: 'Contabilidad' });

        return false;
    }

    if (TasaIGV != 18) {
        alertify.alert('Ingrese la tasa de igv correcta 18%', function () { }).set({ title: 'Contabilidad' });

        return false;
    }

    $.ajax({
        url: BASE_URL + 'app/movements/sales/consulta_tipo_vouchers',
        data: {
            CodTV,
            CodMoneda,
            ValorTC,
            Afecto,
            Inafecto,
            Exonerado,
            Descuento,
            Anticipo,
            ISC,
            Igv,
            Total,
            FecEmision,
            FecVcto,
            IdSocioN,
            CodDocumento,
            Serie,
            NumeroDoc,
            NumeroDocF,
            TipoOperacion,
            CodCondPago,
            tipo: 'nuevo',
        },
        type: 'POST',
        async: false,
        success: function (data) {
            $('#tr_vacio_ingreso_ventas').remove();

            $('#tabla_ingreso_ventas > tbody').html(data);

            autocompletado('select.CodCuenta', { App: 'Ventas' }, BASE_URL + 'app/mantenience/accounting_plan/autocompletado');

            autocompletado('select.CodMoneda', { text: 'Abrev', App: 'Ventas' }, BASE_URL + 'app/moneda/autocompletado_');

            autocompletado('select.IdSocioN', { verDocumento: 'true', App: 'Ventas' }, BASE_URL + 'app/mantenience/business_partner/autocompletado_');

            autocompletado('select.CodDocumento', { App: 'Ventas' }, BASE_URL + 'app/documento/autocompletado');

            autocompletado('select.TipoOperacion', { IdAnexo: 0, TipoAnexo: 5, OtroDato: '', App: 'Ventas' }, BASE_URL + 'app/attached/autocompletado');

            autocompletado('select.CodCcosto', { App: 'Ventas' }, BASE_URL + 'app/mantenience/cost_center/autocompletado');

            autocompletado('select.CodCondPago', { App: 'Ventas' }, BASE_URL + 'app/mantenience/payment_condition/autocompletado_');

            autocompletado('select.Parametro', { App: 'Ventas' }, BASE_URL + 'app/parametro/autocompletado');

            autocompletado('select.IdDetraccion', { App: 'Ventas' }, BASE_URL + 'app/detraccion/autocompletado');

            autocompletado('select.IdTipOpeDetra', { IdAnexo: 0, TipoAnexo: 23, OtroDato: '', App: 'Ventas' }, BASE_URL + 'app/attached/autocompletado');

            autocompletado('select.Declarar_Per', { App: 'Ventas' }, BASE_URL + 'app/declararPeriodo/autocompletado');

            autocompletado('select.Declarar_Est', { IdAnexo: 0, TipoAnexo: 11, OtroDato: '', App: 'Ventas' }, BASE_URL + 'app/attached/autocompletado');

            autocompletado('select.IdActivo', { App: 'Ventas' }, BASE_URL + 'app/mantenience/fixed_assets/autocompletado');

            jQuery('.mydatepicker').datepicker({
                format: 'dd/mm/yyyy',
                'language': 'es',
                autoclose: true
            });

            $('#tab1-tab').removeClass('active');
            $('#tab1-tab').addClass('disabled');
            $('#tab1-tab-pane').removeClass('show active');

            $('#tab2-tab').removeClass('disabled');
            $('#tab2-tab').addClass('active');
            $('#tab2-tab-pane').addClass('show active');

            $('#btnAgregar').addClass('display-none');
            $('#btnAgregarMas').removeClass('display-none');
            $('#btnAgregarMas').show();

            set_suma_total();
        },
    });

    estado_grabar = true;

    return true;
}

function agregar_fila() {
    var estado_agregar = false;
    var NumItem;
    var id;

    $('.Seleccionar').each(function (i) {
        if ($(this).is(':checked')) {
            estado_agregar = true;

            if ($(this).attr('id') == 'SeleccionarUltimo') {
                NumItem = $('.NumItem').length;
            } else {
                NumItem = $(this).attr('id').split('Seleccionar')[1];
            }
        }
    });

    if (estado_agregar) {
        var CodMoneda = $('#CodMoneda1') ? $('#CodMoneda1 option:selected').val() : $('#CodMoneda option:selected').val();
        var ValorTC = $('#ValorTC').val();
        var FecEmision = NumItem == 1 ? $('#FecEmision').val() : $('#FecEmision1').val();
        var FecVcto = NumItem == 1 ? $('#FecVcto').val() : $('#FecVcto1').val();
        var CodDocumento = $('#CodDocumento1') ? $('#CodDocumento1 option:selected').val() : $('#CodDocumento option:selected').val();
        var Serie = $('#Serie').val();
        var NumeroDoc = $('#NumeroDoc').val();
        var NumeroDocF = $('#NumeroDocF').val();
        var Parametro = $('.Parametro').length > 0 ? $('.Parametro').last().val() : '';

        $.ajax({
            url: BASE_URL + 'app/movements/sales/agregar_mas_detalle_fila',
            data: {
                NumItem,
                CodMoneda,
                ValorTC,
                FecEmision,
                FecVcto,
                CodDocumento,
                Serie,
                NumeroDoc,
                NumeroDocF,
                Parametro,
                tipo: 'nuevo',
            },
            type: 'POST',
            async: false,
            success: function (data) {
                $("#tr_vacio_ingreso_ventas").remove();

                $('#SeleccionarUltimo').is(':checked') ? $('#tr_' + NumItem).after(data) : $('#tr_' + NumItem).before(data);

                resetear_filas();

                if (Referencia == 1) {
                    $(".NumItem").each(function (i) {
                        if (
                            $("#CodCuenta" + (i + 1)).val() != null &&
                            !$("#CodCuenta" + (i + 1))
                                .val()
                                .includes("4011")
                        ) {
                            if ($("#CtaCte" + (i + 1)).val() == 0) {
                                $("#CodCuenta" + (i + 1)).removeAttr("disabled");
                                $("#CodMoneda" + (i + 1)).removeAttr("disabled");
                                $("#DebeSol" + (i + 1)).removeAttr("readonly");
                                $("#HaberSol" + (i + 1)).removeAttr("readonly");
                                $("#DebeDol" + (i + 1)).removeAttr("readonly");
                                $("#HaberDol" + (i + 1)).removeAttr("readonly");
                                $("#FecEmision" + (i + 1)).removeAttr("readonly");
                                $("#FecVcto" + (i + 1)).removeAttr("readonly");
                                $("#CodDocumento" + (i + 1)).removeAttr("disabled");
                                $("#SerieDoc" + (i + 1)).removeAttr("readonly");
                                $("#NumeroDoc" + (i + 1)).removeAttr("readonly");
                                $("#NumeroDocF" + (i + 1)).removeAttr("readonly");
                                $("#Parametro" + (i + 1)).removeAttr("disabled");
                                $("#IdDetraccion" + (i + 1)).removeAttr("disabled");
                                $("#IdTipOpeDetra" + (i + 1)).removeAttr("disabled");
                                $("#IdenContProy" + (i + 1)).removeAttr("readonly");
                                $("#IdenContProy" + (i + 1)).removeClass("background-transparente border-none");
                            }
                        }
                    });
                }

                autocompletado('select.CodCuenta', { App: 'Ventas' }, BASE_URL + 'app/mantenience/accounting_plan/autocompletado');

                autocompletado('select.CodMoneda', { text: 'Abrev', App: 'Ventas' }, BASE_URL + 'app/moneda/autocompletado_');

                autocompletado('select.IdSocioN', { verDocumento: 'true', App: 'Ventas' }, BASE_URL + 'app/mantenience/business_partner/autocompletado_');

                autocompletado('select.CodDocumento', { App: 'Ventas' }, BASE_URL + 'app/documento/autocompletado');

                autocompletado('select.TipoOperacion', { IdAnexo: 0, TipoAnexo: 5, OtroDato: '', App: 'Ventas' }, BASE_URL + 'app/attached/autocompletado');

                autocompletado('select.CodCondPago', { App: 'Ventas' }, BASE_URL + 'app/mantenience/payment_condition/autocompletado_');

                autocompletado('select.Parametro', { App: 'Ventas' }, BASE_URL + 'app/parametro/autocompletado');

                autocompletado('select.IdDetraccion', { App: 'Ventas' }, BASE_URL + 'app/detraccion/autocompletado');

                autocompletado('select.IdTipOpeDetra', { IdAnexo: 0, TipoAnexo: 23, OtroDato: '', App: 'Ventas' }, BASE_URL + 'app/attached/autocompletado');

                autocompletado('select.Declarar_Per', { App: 'Ventas' }, BASE_URL + 'app/declararPeriodo/autocompletado');

                autocompletado('select.Declarar_Est', { IdAnexo: 0, TipoAnexo: 11, OtroDato: '', App: 'Ventas' }, BASE_URL + 'app/attached/autocompletado');

                jQuery('.mydatepicker').datepicker({
                    format: 'dd/mm/yyyy',
                    'language': 'es',
                    autoclose: true
                });

                set_suma_total();
            },
        });
    } else {
        alertify.alert('Seleccione un item', function () { }).set({ title: 'Contabilidad' });
    }
}

function eliminar_fila(id) {
    $('#tr_' + id).remove();

    resetear_filas();

    if ($('.clase_ingreso_ventas').length == 0) {
        $('#tabla_ingreso_ventas > tbody').append('<tr id="tr_vacio_ingreso_ventas"><td align="center" colspan="' + $(' #tabla_ingreso_ventas thead tr th').length + '">No hay datos para mostrar</td></tr>');
    }

    set_suma_total();
}

function resetear_filas() {
    $('.clase_ingreso_ventas').each(function (i) {
        $(this).attr('id', 'tr_' + (i + 1));
    });

    $('.Seleccionar').each(function (i) {
        $(this).attr('id', 'Seleccionar' + (i + 1));
    });

    $('.NumItem').each(function (i) {
        $(this).attr('value', i + 1);
        $(this).attr('id', 'NumItem' + (i + 1));
    });

    $('.td_ctacte').each(function (i) {
        $(this).attr('id', 'td_ctacte_' + (i + 1));
    });

    $('.CtaCte').each(function (i) {
        $(this).attr('id', 'CtaCte' + (i + 1));
    });

    $('.CodCuenta').each(function (i) {
        $(this).attr('id', 'CodCuenta' + (i + 1));
        $(this).attr('onchange', 'cambiar_cuenta(' + (i + 1) + ')');
    });

    $('.CodMoneda').each(function (i) {
        $(this).attr('id', 'CodMoneda' + (i + 1));
        $(this).attr('onchange', 'cambiar_moneda(' + (i + 1) + ')');
    });

    $('.ValorTC').each(function (i) {
        $(this).attr('id', 'ValorTC' + (i + 1));
        $(this).attr('oninput', 'cambiar_tipo_cambio_from_table(' + (i + 1) + ')');
    });

    $('.DebeSol').each(function (i) {
        $(this).attr('id', 'DebeSol' + (i + 1));
        $(this).attr('oninput', 'cambiar_debe_soles(' + (i + 1) + ')');
    });

    $('.HaberSol').each(function (i) {
        $(this).attr('id', 'HaberSol' + (i + 1));
        $(this).attr('oninput', 'cambiar_haber_soles(' + (i + 1) + ')');
    });

    $('.DebeDol').each(function (i) {
        $(this).attr('id', 'DebeDol' + (i + 1));
        $(this).attr('oninput', 'cambiar_debe_dolar(' + (i + 1) + ')');
    });

    $('.HaberDol').each(function (i) {
        $(this).attr('id', 'HaberDol' + (i + 1));
        $(this).attr('oninput', 'cambiar_haber_dolar(' + (i + 1) + ')');
    });

    $('.FecEmision').each(function (i) {
        $(this).attr('id', 'FecEmision' + (i + 1));
        $(this).attr('onchange', 'cambiar_fecha_emision(' + (i + 1) + ')');
    });

    $('.FecVcto').each(function (i) {
        $(this).attr('id', 'FecVcto' + (i + 1));
        $(this).attr('onchange', 'cambiar_fecha_vencimiento(' + (i + 1) + ')');
    });

    $('.td_socio_negocio').each(function (i) {
        $(this).attr('id', 'td_socio_negocio_' + (i + 1));
    });

    $('.IdSocioN').each(function (i) {
        $(this).attr('id', 'IdSocioN' + (i + 1));
    });

    $('.CodDocumento').each(function (i) {
        $(this).attr('id', 'CodDocumento' + (i + 1));
    });

    $('.Serie').each(function (i) {
        $(this).attr('id', 'Serie' + (i + 1));
        $(this).attr('oninput', 'verificar_serie_from_table(' + (i + 1) + ')');
        $(this).attr('onfocusout', 'cambiar_serie_from_table(' + (i + 1) + ')');
    });

    $('.NumeroDoc').each(function (i) {
        $(this).attr('id', 'NumeroDoc' + (i + 1));
        $(this).attr('oninput', 'cambiar_numero_inicial(' + (i + 1) + ')');
    });

    $('.NumeroDocF').each(function (i) {
        $(this).attr('id', 'NumeroDocF' + (i + 1));
    });

    $('.td_tipo_operacion').each(function (i) {
        $(this).attr('id', 'td_tipo_operacion_' + (i + 1));
    });

    $('.TipoOperacion').each(function (i) {
        $(this).attr('id', 'TipoOperacion' + (i + 1));
    });

    $('.td_centro_costo').each(function (i) {
        $(this).attr('id', 'td_centro_costo_' + (i + 1));
    });

    $('.CodCcosto').each(function (i) {
        $(this).attr('id', 'CodCcosto' + (i + 1));
    });

    $('.td_condicion_pago').each(function (i) {
        $(this).attr('id', 'td_condicion_pago_' + (i + 1));
    });

    $('.CodCondPago').each(function (i) {
        $(this).attr('id', 'CodCondPago' + (i + 1));
    });

    $('.td_documento_retencion').each(function (i) {
        $(this).attr('id', 'td_documento_retencion_' + (i + 1));
    });

    $('.DocRetencion').each(function (i) {
        $(this).attr('id', 'DocRetencion' + (i + 1));
    });

    $('.DocDetraccion').each(function (i) {
        $(this).attr('id', 'DocDetraccion' + (i + 1));
    });

    $('.Parametro').each(function (i) {
        $(this).attr('id', 'Parametro' + (i + 1));
        $(this).attr('onchange', 'cambiar_parametro(' + (i + 1) + ')');
    });

    $('.PorcRetencion').each(function (i) {
        $(this).attr('id', 'PorcRetencion' + (i + 1));
    });

    $('.IdDetraccion').each(function (i) {
        $(this).attr('id', 'IdDetraccion' + (i + 1));
    });

    $('.FechaDetraccion').each(function (i) {
        $(this).attr('id', 'FechaDetraccion' + (i + 1));
    });

    $('.IdTipOpeDetra').each(function (i) {
        $(this).attr('id', 'IdTipOpeDetra' + (i + 1));
    });

    $('.IdenContProy').each(function (i) {
        $(this).attr('id', 'IdenContProy' + (i + 1));
    });

    $('.Declarar_Per').each(function (i) {
        $(this).attr('id', 'Declarar_Per' + (i + 1));
    });

    $('.Declarar_Est').each(function (i) {
        $(this).attr('id', 'Declarar_Est' + (i + 1));
    });

    $('.td_activo_fijo').each(function (i) {
        $(this).attr('id', 'td_activo_fijo_' + (i + 1));
    });

    $('.IdActivo').each(function (i) {
        $(this).attr('id', 'IdActivo' + (i + 1));
    });

    $('.Eliminar').each(function (i) {
        $(this).attr('id', 'Eliminar' + (i + 1));
        $(this).attr('onclick', 'eliminar_fila(' + (i + 1) + ')');
    });
}

function verificar_formulario_cliente() {
    var ApePat = $('#ApePat').val();
    var ApeMat = $('#ApeMat').val();
    var Nom1 = $('#Nom1').val();
    var CodTipPer = $('#CodTipPer').val();
    var CodTipoDoc = $('#CodTipoDoc').val();
    var TipoDato = CodTipoDoc != null ? $('#CodTipoDoc option:selected').attr('data-tipo-dato').split('|') : '';
    var existe_duplicados = false;
    var existe_codigo = '';

    if (CodTipPer == null) {
        alertify.alert('Debe Ingresar Tipo Persona!', function () { }).set({ title: 'Contabilidad' });

        return false;
    }

    if (CodTipoDoc == null) {
        alertify.alert('Debe Ingresar Tipo Documento!', function () { }).set({ title: 'Contabilidad' });

        return false;
    }

    if (CodTipPer == datos_ruc_CodTipPer && CodTipoDoc == datos_ruc_CodTipoDoc) {
        $('#docidentidad').val('');
    } else {
        $('#ruc').val('');
    }

    if (
        (CodTipPer != datos_ruc_CodTipPer && CodTipoDoc == datos_ruc_CodTipoDoc) ||
        (CodTipPer == datos_ruc_CodTipPer && CodTipoDoc != datos_ruc_CodTipoDoc)
    ) {
        alertify.alert('Debe de Seleccionar el Tipo de Persona y el Tipo de Documento de Identidad correctos!', function () { }).set({ title: 'Contabilidad' });

        return false;
    }

    var ruc = $('#ruc').val();
    var docidentidad = $('#docidentidad').val();
    var razonsocial = $('#razonsocial').val();

    if (
        ApePat.length == 0 &&
        CodTipPer != datos_ruc_CodTipPer &&
        CodTipPer != datos_extranjero_CodTipPer &&
        CodTipoDoc != datos_extranjero_CodTipoDoc
    ) {
        alertify.alert('Debe de Registrar el A. Paterno del Cliente!', function () { }).set({ title: 'Contabilidad' });

        return false;
    }

    if (
        ApeMat.length == 0 &&
        CodTipPer != datos_ruc_CodTipPer &&
        CodTipPer != datos_extranjero_CodTipPer &&
        CodTipoDoc != datos_extranjero_CodTipoDoc
    ) {
        alertify.alert('Debe de Registrar el A. Materno del Cliente!', function () { }).set({ title: 'Contabilidad' });

        return false;
    }

    if (
        Nom1.length == 0 &&
        CodTipPer != datos_ruc_CodTipPer &&
        CodTipPer != datos_extranjero_CodTipPer &&
        CodTipoDoc != datos_extranjero_CodTipoDoc
    ) {
        alertify.alert('Debe de Registrar el Nombre del Cliente!', function () { }).set({ title: 'Contabilidad' });

        return false;
    }

    if (
        razonsocial.length == 0 &&
        (CodTipPer == datos_ruc_CodTipPer ||
            (CodTipPer == datos_extranjero_CodTipPer &&
                CodTipoDoc == datos_extranjero_CodTipoDoc))
    ) {
        alertify.alert('Debe de Registrar la Razón Social!', function () { }).set({ title: 'Contabilidad' });

        return false;
    }

    if (
        docidentidad.length == 0 &&
        CodTipPer != datos_ruc_CodTipPer &&
        CodTipoDoc != datos_extranjero_CodTipoDoc
    ) {
        alertify.alert('Debe Ingresar DNI!', function () { }).set({ title: 'Contabilidad' });

        return false;
    }

    if (
        docidentidad.length == 0 &&
        CodTipoDoc != datos_ruc_CodTipoDoc &&
        CodTipoDoc != datos_extranjero_CodTipoDoc
    ) {
        alertify.alert('Debe Ingresar DNI!', function () { }).set({ title: 'Contabilidad' });

        return false;
    }

    if (
        ruc.length == 0 &&
        CodTipPer == datos_ruc_CodTipPer &&
        CodTipoDoc != datos_extranjero_CodTipoDoc
    ) {
        alertify.alert('Debe Ingresar RUC!', function () { }).set({ title: 'Contabilidad' });

        return false;
    }

    if (
        ruc.length == 0 &&
        CodTipoDoc == datos_ruc_CodTipoDoc &&
        CodTipoDoc != datos_extranjero_CodTipoDoc
    ) {
        alertify.alert('Debe Ingresar RUC!', function () { }).set({ title: 'Contabilidad' });

        return false;
    }

    $.ajax({
        url: BASE_URL + 'app/mantenience/business_partner/consulta_duplicados',
        data: {
            tipo: 'nuevo',
            ruc,
            docidentidad,
            razonsocial,
        },
        type: 'POST',
        async: false,
        success: function (data) {
            var datos = JSON.parse(data);

            existe_duplicados = datos.existe;
            existe_codigo = datos.codigo;
        },
    });

    if (existe_duplicados) {
        alertify.alert('Ya existe el N° de Documento: ' + existe_codigo, function () { }).set({ title: 'Contabilidad' });

        return false;
    }

    if (TipoDato.length == 3) {
        var longitud = parseInt(TipoDato[0]);
        var tipo_dato = TipoDato[1];
        var tipo_longitud = TipoDato[2];

        if (
            docidentidad.length > 0 &&
            docidentidad.length < longitud &&
            tipo_longitud == datos_ruc_N_tip &&
            CodTipPer != datos_ruc_CodTipPer
        ) {
            alertify.alert('Debe Ingresar hasta ' + longitud + ' dígitos!', function () { });

            return false;
        }

        if (
            ruc.length > 0 &&
            ruc.length < longitud &&
            tipo_longitud == datos_ruc_N_tip &&
            CodTipPer == datos_ruc_CodTipPer
        ) {
            alertify.alert('Debe Ingresar hasta ' + longitud + ' dígitos!', function () { });

            return false;
        }
    }

    $.ajax({
        url: BASE_URL + 'app/movements/sales/registrar_socio_negocio',
        data: $('#clienteForm').serialize(),
        type: 'POST',
        async: false,
        success: function (data) {
            var datos = JSON.parse(data);

            if (datos.estado) {
                $('#IdSocioN').html(datos.option).trigger('change');

                $('#clienteModal').modal('toggle');

                alertify.success('Cliente registrado correctamente!');
            } else {
                alertify.error('Error. Consulte con el administrador');
            }
        },
    });
}

function verificarFormularioBanco() {
    var CodCuentaBanco = $('#CodCuentaBanco option:selected').val();
    var CodTipoPagoBanco = $('#CodTipoPagoBanco option:selected').val();

    if (CodCuentaBanco == null) {
        $('#CodCuentaBanco').focus();

        alertify.alert('Seleccionar Cuenta Contable (Banco)', function () { }).set({ title: 'Contabilidad' });
    } else if (CodTipoPagoBanco == null) {
        $('#CodTipoPagoBanco').focus();

        alertify.alert('Seleccionar Tipo de Pago', function () { }).set({ title: 'Contabilidad' });
    } else {
        var datos = $('#formBanco').serialize();

        $('#Banco').val(datos);

        $('#form').submit();
    }
}

function verificarFormulario() {
    var Codmov = $('#Codmov').val();
    var Glosa = $('#Glosa').val();
    // var CodCuenta = 0;
    // var estado_CodCuenta = false;
    var IdSocioN = 0;
    var estado_IdSocioN = false;
    var TipoOperacion = 0;
    var estado_TipoOperacion = false;
    var CodCondPago = 0;
    var estado_CodCondPago = false;
    var CodCcosto = 0;
    var estado_CodCcosto = false;
    var Parametro = 0;
    var estado_Parametro = false;
    var IdActivo = 0;
    var estado_IdActivo = false;
    var referencia_Total = parseFloat($('#referencia_Total').val().replace(',', ''));
    var CodInterno = $('#FormaPago option:selected').attr('data-codigo-interno');

    var CodMoneda = $('.CodMoneda').length == 0 ? $('#CodMoneda').val() : $('.CodMoneda').first().val();

    var total_DebeSol = parseFloat($('#total_DebeSol').val());
    var total_HaberSol = parseFloat($('#total_HaberSol').val());
    var total_DebeDol = parseFloat($('#total_DebeDol').val());
    var total_HaberDol = parseFloat($('#total_HaberDol').val());

    // $('select.CodCuenta').each(function (i) {
    //     if (this.value.length == 0) {
    //         CodCuenta = $(this).attr('id');
    //         estado_CodCuenta = true;

    //         return false;
    //     }
    // });

    $('.IdSocioN').each(function (i) {
        if (this.value == null) {
            IdSocioN = $(this).attr('id');
            estado_IdSocioN = true;
            return false;
        }
    });

    $('.TipoOperacion').each(function (i) {
        if (this.value == null) {
            TipoOperacion = $(this).attr('id');
            estado_TipoOperacion = true;
            return false;
        }
    });

    $('.CodCondPago').each(function (i) {
        if (this.value == null) {
            CodCondPago = $(this).attr('id');
            estado_CodCondPago = true;
            return false;
        }
    });

    $('.CodCcosto').each(function (i) {
        if (this.value == null) {
            CodCcosto = $(this).attr('id');
            estado_CodCcosto = true;

            return false;
        }
    });

    $('.Parametro').each(function (i) {
        if (this.value == null) {
            Parametro = $(this).attr('id');
            estado_Parametro = true;

            return false;
        }
    });

    $('.IdActivo').each(function (i) {
        if (this.value == null) {
            IdActivo = $(this).attr('id');
            estado_IdActivo = true;

            return false;
        }
    });

    if (Glosa.length == 0) {
        alertify.alert("Ingrese Glosa, para Asiento Contable", function () { }).set({ title: 'Contabilidad' });
    } else if (estado_IdSocioN) {
        $("#" + IdSocioN).focus();

        alertify.alert("Ingrese Razón Social de esta Cuenta", function () { }).set({ title: 'Contabilidad' });
    } else if (estado_TipoOperacion) {
        $("#" + TipoOperacion).focus();

        alertify.alert("Ingrese Tipo de Operación", function () { }).set({ title: 'Contabilidad' });
    } else if (estado_CodCondPago) {
        $("#" + CodCondPago).focus();

        alertify.alert("Ingrese Condición de Pago", function () { }).set({ title: 'Contabilidad' });
    } else if (estado_CodCcosto) {
        $("#" + CodCcosto).focus();

        alertify.alert("Ingrese el Centro de Costo de esta Cuenta", function () { }).set({ title: 'Contabilidad' });
    } else if (estado_Parametro) {
        $("#" + Parametro).focus();

        alertify.alert("Ingrese el Parametro de esta Cuenta", function () { }).set({ title: 'Contabilidad' });
    } else if (estado_IdActivo) {
        $("#" + IdActivo).focus();

        alertify.alert("Ingrese el Activo Fijo de esta Cuenta", function () { }).set({ title: 'Contabilidad' });
    } else if ($(".tr_referencia").length > 0 && (referencia_Total == 0 || (CodMoneda == "MO002" ? referencia_Total != total_HaberDol : referencia_Total != total_HaberSol))) {
        alertify.alert("El monto total de las referencias debe ser igual al total de la Nota de Crédito", function () { }).set({ title: 'Contabilidad' });
    } else if (total_DebeSol != total_HaberSol) {
        alertify.confirm("El Total Debe Soles es diferente con el Total Haber Soles<br>Desea Continuar..!",
            function (e) {
                if (e) {
                    setTimeout(() => {
                        if (total_DebeDol != total_HaberDol) {
                            alertify.confirm("El Total Debe Dolar es diferente con el Total Haber Dolar<br>Desea Continuar..!",
                                function (e) {
                                    if (e) {
                                        setTimeout(() => {
                                            if (CodInterno == 2) {
                                                $("#bancoModal").modal("show");
                                            } else {
                                                $("#form").submit();
                                            }
                                        }, 500);
                                    }
                                }
                            ).set({ title: 'Contabilidad' }).set("labels", { ok: "Si", cancel: "No" });
                        }
                    }, 500);
                }
            }
        ).set({ title: 'Contabilidad' }).set("labels", { ok: "Si", cancel: "No" });
    } else if (total_DebeDol != total_HaberDol) {
        alertify.confirm("El Total Debe Dolar es diferente con el Total Haber Dolar<br>Desea Continuar..!",
            function (e) {
                if (e) {
                    setTimeout(() => {
                        if (CodInterno == 2) {
                            $("#bancoModal").modal("show");
                        } else {
                            $("#form").submit();
                        }
                    }, 500);
                }
            }
        ).set({ title: 'Contabilidad' }).set("labels", { ok: "Si", cancel: "No" });
    } else if (
        total_DebeSol == total_HaberSol &&
        total_DebeDol == total_HaberDol
    ) {
        if (CodInterno == 2) {
            $("#bancoModal").modal("show");
        } else {
            $("#form").submit();
        }
    }
}