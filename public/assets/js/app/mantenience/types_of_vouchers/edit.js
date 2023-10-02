$('select').select2({
    width: 'auto',
    dropdownAutoWidth: true
});

autocompletado($('.CodCuenta'), BASE_URL + "app/mantenience/accounting_plan/autocompletado");

var td_tipo_1 = '';

function cambiarTabla() {
    var thead = '';
    var Tipo = parseInt($('#Tipo option:selected').val());

    /* if(Tipo == 1){
        $('#CodTVcaja').attr('disabled', false);
        $('#CodTVcaja').html(options_tipo_1);
    }else if(Tipo == 3){
        $('#CodTVcaja').attr('disabled', false);
        $('#CodTVcaja').html(options_tipo_3);
    }else if(Tipo == 7){
        $('#CodTVcaja').attr('disabled', false);
        $('#CodTVcaja').html(options_tipo_7);
    }else{
        $('#CodTVcaja').attr('disabled', true);
        $('#CodTVcaja').html('');
    }

    $('#CodTVcaja').select2({
        width: 'auto',
        dropdownAutoWidth: true
    }); */

    switch (Tipo) {
        case 0:
            thead = `
                    <tr>
                        <th>N°</th>
                        <th>Cuenta</th>
                        <th>D / H</th>
                        <th>Monto</th>
                        <th>C. Costo</th>
                        <th>Act. Fijo</th>
                        <th>Raz. Social</th>
                        <th>Eliminar</th>
                    </tr>
                `;

            break;

        case 1:
        case 2:
        case 3:
        case 4:
        case 5:
        case 6:
        case 7:
        case 8:
        case 9:
            var th = Tipo != 9 ? '<th>Moneda</th>' : '';

            thead = `
                    <tr>
                        <th>N°</th>
                        <th>Cuenta</th>
                        <th>D / H</th>
                        <th>Parametro</th>
                        ${th}
                        <th>Monto</th>
                        <th>C. Costo</th>
                        <th>Act. Fijo</th>
                        <th>Raz. Social</th>
                        <th>Eliminar</th>
                    </tr>
                `;

            break;
    }

    $('#tabla_tipo_vouchers > thead').html(thead);

    if ($('.clase_tipo_vouchers').length == 0) {
        empty = `
                    <tr id="tr_vacio_tipo_vouchers">
                        <td align="center" colspan="${$('#tabla_tipo_vouchers thead tr th').length}">No hay datos para mostrar</td>
                    </tr>
            `;

        $('#tabla_tipo_vouchers > tbody').html(empty);
    } else {
        $('.clase_tipo_vouchers').each(function (i) {
            switch (Tipo) {
                case 0:
                    $(this).children('td').removeClass('background-readonly');

                    $('.tdTipo0').removeClass('display-none');
                    $('.tdTipo0').show()

                    $('.tdTipo1').removeClass('display-block');
                    $('.tdTipo1').hide()

                    $('.MontoD').removeClass('display-none');
                    $('.MontoD').addClass('display-block');

                    $('.CodCcosto').removeClass('display-none');
                    $('.CodCcosto').addClass('display-block');
                    $('.CodCcosto').select2({
                        width: 'auto',
                        dropdownAutoWidth: true
                    });

                    $('.IdActivo').removeClass('display-none');
                    $('.IdActivo').addClass('display-block');
                    $('.IdActivo').select2({
                        width: 'auto',
                        dropdownAutoWidth: true
                    });

                    $('.IdSocioN').removeClass('display-none');
                    $('.IdSocioN').addClass('display-block');
                    $('.IdSocioN').select2({
                        width: 'auto',
                        dropdownAutoWidth: true
                    });

                    break;

                case 1:
                case 2:
                case 3:
                case 4:
                case 5:
                case 6:
                case 7:
                case 8:
                case 9:
                    $(this).children('td').removeClass('background-readonly');

                    $('.tdTipoBackground1').addClass('background-readonly');

                    $('.tdTipo0').removeClass('display-none');
                    $('.tdTipo0').show();

                    $('.tdTipo1').removeClass('display-none');
                    $('.tdTipo1').show();

                    $('.CodMoneda').removeClass('display-block');
                    $('.CodMoneda').addClass('display-none');

                    $('.MontoD').removeClass('display-block');
                    $('.MontoD').addClass('display-none');

                    $('.CodCcosto').removeClass('display-block');
                    $('.CodCcosto').addClass('display-none');
                    $('.CodCcosto').select2({
                        width: 'auto',
                        dropdownAutoWidth: true
                    });

                    $('.IdActivo').removeClass('display-block');
                    $('.IdActivo').addClass('display-none');
                    $('.IdActivo').select2({
                        width: 'auto',
                        dropdownAutoWidth: true
                    });

                    $('.IdSocioN').removeClass('display-block');
                    $('.IdSocioN').addClass('display-none');
                    $('.IdSocioN').select2({
                        width: 'auto',
                        dropdownAutoWidth: true
                    });

                    if (Tipo == 5 || Tipo == 6) {
                        $('.tdTipo9').removeClass('background-readonly');

                        $('.CodMoneda').removeClass('display-none');
                        $('.CodMoneda').addClass('display-block');
                    }

                    if (Tipo == 9) $('.tdTipo9').hide();

                    $(".CodCuenta").each(function (i) {
                        var index = this.id.split('CodCuenta')[1];

                        var RelacionCuenta = $('#CodCuenta' + index + ' option:selected').attr('data-relacion-cuenta');

                        if (RelacionCuenta == '1' || RelacionCuenta == '3') {
                            $('#td_CodMoneda_' + index).removeClass('background-readonly');
                            $('#CodMoneda' + index).removeClass('display-none');
                            $('#CodMoneda' + index).addClass('display-block');
                        } else {
                            $('#td_CodMoneda_' + index).addClass('background-readonly');
                            $('#CodMoneda' + index).removeClass('display-block');
                            $('#CodMoneda' + index).addClass('display-none');
                        }
                    });


                    break;
            }
        });
    }
}

function cambiar_cuenta(id) {
    var RelacionCuenta = $('#CodCuenta' + id + ' option:selected').attr('data-relacion-cuenta');

    if (RelacionCuenta == '1' || RelacionCuenta == '3') {
        $('#td_CodMoneda_' + id).removeClass('background-readonly');
        $('#CodMoneda' + id).removeClass('display-none');
        $('#CodMoneda' + id).addClass('display-block');
    } else {
        $('#td_CodMoneda_' + id).addClass('background-readonly');
        $('#CodMoneda' + id).removeClass('display-block');
        $('#CodMoneda' + id).addClass('display-none');
    }
}

function nuevaFilaTipoVouchers() {
    $('#tr_vacio_tipo_vouchers').remove();

    var Tipo = parseInt($('#Tipo option:selected').val());
    var background = '';
    var background_ventas = '';
    var display = '';
    var display_ventas = '';

    var nuevo = `
            <tr id="tr_tipo_vouchers${id_tipo_vouchers}" class="clase_tipo_vouchers">
                <td>
                    <input type="text" name="NumItem[]" class="NumItem form-control form-control-sm" value="${id_tipo_vouchers}" readonly />
                </td>
                <td>
                    <select name="CodCuenta[]" class="CodCuenta form-control form-control-sm" id="CodCuenta${id_tipo_vouchers}" onchange="cambiar_cuenta(${id_tipo_vouchers})">

                    </select>
                </td>
                <td>
                    <select name="Debe_Haber[]" class="Debe_Haber form-control form-control-sm" id="Debe_Haber${id_tipo_vouchers}">
                        ${options_debe_haber}
                    </select>
                </td>
            `;

    if (Tipo >= 1 && Tipo <= 9) {
        background = 'background-readonly';
        display = 'display-none';
    }

    td_tipo_1 = `
                <td class="tdTipo1">
                    <select name="Parametro[]" class="Parametro form-control form-control-sm" id="Parametro${id_tipo_vouchers}">
                        ${options_parametro}
                    </select>
                </td>
                <td class="tdTipo1 tdTipo9 tdTipoBackground1 ${background}" id="td_CodMoneda_${id_tipo_vouchers}">
                    <select name="CodMoneda[]" class="CodMoneda form-control form-control-sm ${display}" id="CodMoneda${id_tipo_vouchers}">
                        ${options_moneda}
                    </select>
                </td>
            `;

    nuevo += td_tipo_1;

    nuevo += `<td class="tdTipo0 tdTipoBackground1 ${background}">
                    <input type="text" name="MontoD[]" class="MontoD form-control form-control-sm ${display}" oninput="esMayorCero(this)" onkeypress="esNumero(event)" />
                </td>
                <td class="tdTipo0 tdTipoBackground1 ${background}">
                    <select name="CodCcosto[]" class="CodCcosto form-control form-control-sm ${display}" id="CodCcosto${id_tipo_vouchers}">
                        ${options_centro_costo}
                    </select>
                </td>
                <td class="tdTipo0 tdTipoBackground1 ${background}">
                    <select name="IdActivo[]" class="IdActivo form-control form-control-sm ${display}" id="IdActivo${id_tipo_vouchers}">
                        ${options_activo_fijo}
                    </select>
                </td>
                <td class="tdTipo0 tdTipoBackground1 ${background}">
                    <select name="IdSocioN[]" class="IdSocioN form-control form-control-sm ${display}" id="IdSocioN${id_tipo_vouchers}">
                        ${options_socio_negocio}
                    </select>
                </td>
                <td align="center">
                    <button type="button" class="Buttons btn btn-sm btn-danger shadow-sm" onclick="eliminarFilaTipoVouchers('${id_tipo_vouchers}')">Eliminar</button>
                </td>
            </tr>
        `;

    $('#tabla_tipo_vouchers > tbody').append(nuevo);

    autocompletado($('#CodCuenta' + id_tipo_vouchers), BASE_URL + "app/mantenience/accounting_plan/autocompletado");

    $('#Parametro' + id_tipo_vouchers).select2({
        width: 'auto',
        dropdownAutoWidth: true
    });

    $('#CodCcosto' + id_tipo_vouchers).select2({
        width: 'auto',
        dropdownAutoWidth: true
    });

    $('#IdActivo' + id_tipo_vouchers).select2({
        width: 'auto',
        dropdownAutoWidth: true
    });

    $('#IdSocioN' + id_tipo_vouchers).select2({
        width: 'auto',
        dropdownAutoWidth: true
    });

    switch (Tipo) {
        case 0:
            $('.tdTipo1').hide();

            break;
        case 1:
            $('.tdTipo1').show();

            break;
        case 5:
        case 6:
            $('.tdTipo9').removeClass('background-readonly');

            $('.CodMoneda').removeClass('display-none');
            $('.CodMoneda').addClass('display-block');

            break;
        case 9:
            $('.tdTipo9').hide();

            break;
    }

    id_tipo_vouchers++;
}

function eliminarFilaTipoVouchers(id) {
    var Tipo = parseInt($('#Tipo option:selected').val());

    $('#tr_tipo_vouchers' + id).remove();

    $(".clase_tipo_vouchers").each(function (i) {
        this.id = 'tr_tipo_vouchers' + (i + 1);
    });

    $(".NumItem").each(function (i) {
        this.value = i + 1;
    });

    $(".CodCuenta > select").each(function (i) {
        this.id = this.name.split('[]')[0] + (i + 1);
        $(this).select2({
            width: 'auto',
            dropdownAutoWidth: true
        });
    });

    $(".DescCuenta").each(function (i) {
        this.id = this.name.split('[]')[0] + (i + 1);
    });

    $(".Debe_Haber").each(function (i) {
        this.id = this.name.split('[]')[0] + (i + 1);
    });

    $(".Parametro > select").each(function (i) {
        this.id = this.name.split('[]')[0] + (i + 1);
        $(this).select2({
            width: 'auto',
            dropdownAutoWidth: true
        });
    });

    $(".tdTipo9").each(function (i) {
        this.id = 'td_CodMoneda_' + (i + 1);
    });

    $(".CodMoneda").each(function (i) {
        this.id = this.name.split('[]')[0] + (i + 1);
    });

    $(".MontoD").each(function (i) {
        this.id = this.name.split('[]')[0] + (i + 1);
    });

    $(".CodCcosto > select").each(function (i) {
        this.id = this.name.split('[]')[0] + (i + 1);
        $(this).select2({
            width: 'auto',
            dropdownAutoWidth: true
        });
    });

    $(".IdActivo > select").each(function (i) {
        this.id = this.name.split('[]')[0] + (i + 1);
        $(this).select2({
            width: 'auto',
            dropdownAutoWidth: true
        });
    });

    $(".IdSocioN > select").each(function (i) {
        this.id = this.name.split('[]')[0] + (i + 1);
        $(this).select2({
            width: 'auto',
            dropdownAutoWidth: true
        });
    });

    $(".Buttons").each(function (i) {
        $(this).attr('onclick', 'eliminarFilaTipoVouchers(' + (i + 1) + ')')
    });

    if ($('.clase_tipo_vouchers').length == 0) {
        $('#tabla_tipo_vouchers > tbody').append('<tr id="tr_vacio_tipo_vouchers"><td align="center" colspan="' + $('#tabla_tipo_vouchers thead tr th').length + '">No hay datos para mostrar</td></tr>');
    }

    id_tipo_vouchers = $(".clase_tipo_vouchers").length + 1;
}

function verificarFormulario() {
    var CodTV = $('#CodTV').val().trim().toUpperCase();
    var existe_codigo = false;
    var codigo = '';
    var tipo = '';
    var DescVoucher = $('#DescVoucher').val().trim().toUpperCase();
    var GlosaVoucher = $('#GlosaVoucher').val();
    var existe_cod_cuenta = false;
    var existe_debe_haber = false;
    var existe_parametro = false;
    // var array_cuenta = $(".CodCuenta option:selected").toArray().map(item => item.value);
    var mensaje_error = '';
    var repite_valor_cuenta = 0;

    $('.CodCuenta').removeClass('border-rojo');
    $('.Debe_Haber').removeClass('border-rojo');
    $('.Parametro').removeClass('border-rojo');

    if (CodTV.length == 0) {
        alertify.alert('Registre el Código del Tipo de Vouchers!!', function () { }).set({ title: "Error" });

        return false;
    }

    $.ajax({
        'url': BASE_URL + 'app/mantenience/types_of_vouchers/consulta_codigo',
        'data': { CodTV, DescVoucher, tipo: 'editar', NotCodTV: tipoVoucherCab_CodTV, NotDescVoucher: tipoVoucherCab_DescVoucher },
        'type': 'POST',
        'async': false,
        success: function (data) {
            var datos = JSON.parse(data);

            existe_codigo = datos.estado;
            codigo = datos.codigo;
            tipo = datos.tipo;
        }
    });

    if (existe_codigo) {
        var mensaje = tipo == 'codigo' ? 'Código del Voucher' : 'un Tipo de Voucher';

        alertify.alert('Ya Existe ' + mensaje + ' &rarr; ' + codigo + '<br>Modifique los datos y vuelva a Intentarlo', function () { }).set({ title: "Error" });

        return false;
    }

    if (DescVoucher.length == 0) {
        alertify.alert('Registre la Descripción del Tipo de Vouchers!!', function () { }).set({ title: "Error" });

        return false;
    }

    if (GlosaVoucher.length == 0) {
        alertify.alert('Registre la Glosa del Tipo de Vouchers!!', function () { }).set({ title: "Error" });

        return false;
    }

    if ($(".clase_tipo_vouchers").length == 0 || $('#CodCuenta1').val() == null) {
        alertify.alert('El 1er Registro esta en Blanco ó No Hay Detalle que Grabar!!!', function () { }).set({ title: "Error" });

        return false;
    }

    var index = 0;

    $(".NumItem").each(function (i) {
        if ($('#CodCuenta' + (i + 1)).val() == null || $('#CodCuenta' + (i + 1)).val().length == 0) {
            index = i + 1;
            existe_cod_cuenta = true;
            return false;
        }

        if ($('#Debe_Haber' + (i + 1)).val() == null) {
            index = i + 1;
            existe_debe_haber = true;
            return false;
        }

        if ($('.tdTipo1').is(':visible')) {
            if ($('#Parametro' + (i + 1)).val() == null) {
                index = i + 1;
                existe_parametro = true;
                return false;
            }
        }
    });

    if (existe_cod_cuenta) {
        $('#CodCuenta' + index).addClass('border-rojo');
        $('#CodCuenta' + index).select2({
            width: 'auto',
            dropdownAutoWidth: true
        });

        alertify.alert('Indique el Código de Cuenta!!', function () { }).set({ title: "Error" });

        return false;
    }

    if (existe_debe_haber) {
        $('#Debe_Haber' + index).addClass('border-rojo');

        alertify.alert('Indique si la cuenta va al Debe o al Haber!!', function () { }).set({ title: "Error" });

        return false;
    }

    if (existe_parametro) {
        $('#Parametro' + index).addClass('border-rojo');
        $('#Parametro' + index).select2({
            width: 'auto',
            dropdownAutoWidth: true
        });

        alertify.alert('Indique el tipo de Parametro!!', function () { }).set({ title: "Error" });

        return false;
    }

    // $(".NumItem").each(function(i) {
    //     if(array_cuenta.filter(x => x == array_cuenta[i]).length > 1){
    //         var array_objetos = [];
    //         repite_valor_cuenta = 0;
    //         mensaje_error = 'La Cuenta: <<' +  array_cuenta[i] + '>> Ya Existe!!<br>Modifique y Vuelva Intentarlo!!';

    //         $('.CodCuenta > select').each(function(j) {
    //             if(this.value == array_cuenta[i]){
    //                 array_objetos.push({
    //                     'cuenta': this.value,
    //                     'debe_haber': $('#Debe_Haber' + (j + 1)).val(),
    //                     'index': j + 1
    //                 });
    //             }
    //         });

    //         for (let index = 0; index < array_objetos.length; index++) {
    //             if(array_objetos.filter(x => x.debe_haber == array_objetos[index]['debe_haber']).length > 1){
    //                 repite_valor_cuenta++;
    //                 $('#CodCuenta' + array_objetos[index]['index']).addClass('border-rojo');
    //                 $('#CodCuenta' + array_objetos[index]['index']).select2({
    //                      width: 'auto',
    //                      dropdownAutoWidth: true
    //                 });
    //                 $('#Debe_Haber' + array_objetos[index]['index']).addClass('border-rojo');
    //             }
    //         }

    //         if(repite_valor_cuenta == 0){
    //             $('.CodCuenta').removeClass('border-rojo');
    //             $('.Debe_Haber').removeClass('border-rojo');
    //         }else{
    //             return false;
    //         }
    //     }
    // });

    // if(repite_valor_cuenta > 0){
    //     alertify.alert(mensaje_error, function(){}).set({ title: "Error" });

    //     return false;
    // }

    return true;
}

function submit() {
    $('#form').submit();
}