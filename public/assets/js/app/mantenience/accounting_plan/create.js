$('select').select2({
    width: 'auto', dropdownAutoWidth: true
});

var id_amarre = 1;

function getCuentaPadre() {
    var CodCuenta = $('#CodCuenta').val();

    $.ajax({
        'url': BASE_URL + 'app/mantenience/accounting_plan/consulta_cuenta',
        'data': { CodCuenta, tipo: 'consulta_cuenta_padre' },
        'type': 'POST',
        'async': false,
        success: function (data) {
            var datos = JSON.parse(data);

            $('#CuentaPadre').val(datos.codigo);
        }
    });

    $('#Amarres').prop('checked', false);
    $('#columnaAmarres').removeClass('d-block');
    $('#columnaAmarres').addClass('d-none');
    $('#columnaAmarresVacio').removeClass('d-none');
    $('#columnaAmarresVacio').addClass('d-block');
}

function mostrarActivoFijo() {
    if ($('#RelacionCuenta').val() == 4) {
        $('#columnaActivoFijo').removeClass('d-none');
        $('#columnaActivoFijo').addClass('d-block');
        $('#columnaActivoFijoVacio').removeClass('d-block');
        $('#columnaActivoFijoVacio').addClass('d-none');
    } else {
        $('#columnaActivoFijo').removeClass('d-block');
        $('#columnaActivoFijo').addClass('d-none');
        $('#columnaActivoFijoVacio').removeClass('d-none');
        $('#columnaActivoFijoVacio').addClass('d-block');
    }
}

function mostrarTipoDebeHaber() {
    var TipoResultado = $('#TipoResultado option:selected').val();

    $(".TipoDebeHaber").each(function (i) {
        $('#TipoDebeHaber' + i).val(i);
        $('#TipoDebeHaber' + i).attr('disabled', true);
        $('#labelTipoDebeHaber' + i).addClass('disabled');

        if (TipoResultado == 0) {
            if (i == 0) {
                $('#TipoDebeHaber' + i).attr('disabled', false);
                $('#labelTipoDebeHaber' + i).removeClass('disabled');
            }
        }

        if (TipoResultado == 1) {
            if (i <= 2 || i == 5) {
                $('#TipoDebeHaber' + i).attr('disabled', false);
                $('#labelTipoDebeHaber' + i).removeClass('disabled');
            }
        }

        if (TipoResultado == 2 || TipoResultado == 3 || TipoResultado == 4) {
            if (i == 0 || i == 3 || i == 4) {
                $('#TipoDebeHaber' + i).attr('disabled', false);
                $('#labelTipoDebeHaber' + i).removeClass('disabled');
            }
        }
    });
}

function mostrarAjusteDC() {
    if ($('#AjusteDC').is(':checked')) {
        $('#AjusteDC').val('1');
        $('#columnaTcambio_CV').removeClass('d-none');
        $('#columnaTcambio_CV').addClass('d-block');
    } else {
        $('#AjusteDC').val('0');
        $('#columnaTcambio_CV').removeClass('d-block');
        $('#columnaTcambio_CV').addClass('d-none');
    }
}

function mostrarTablaAmarres() {
    if ($('#Amarres').is(':checked')) {
        var CodCuenta = $('#CodCuenta').val();
        var CuentaPadre = $('#CuentaPadre').val();
        var permite_amarre = false;

        $.ajax({
            'url': BASE_URL + 'app/mantenience/accounting_plan/consulta_cuenta',
            'data': { CodCuenta, CuentaPadre, tipo: 'verificar_ultimo_hijo', subtipo: 'nuevo' },
            'type': 'POST',
            'async': false,
            success: function (data) {
                var datos = JSON.parse(data);

                permite_amarre = datos.permite;
            }
        });

        if (permite_amarre) {
            if ($('#Amarres').is(':checked')) {
                $('#columnaAmarres').removeClass('d-none');
                $('#columnaAmarres').addClass('d-block');
                $('#columnaAmarresVacio').removeClass('d-block');
                $('#columnaAmarresVacio').addClass('d-none');
            } else {
                $('#columnaAmarres').removeClass('d-block');
                $('#columnaAmarres').addClass('d-none');
                $('#columnaAmarresVacio').removeClass('d-none');
                $('#columnaAmarresVacio').addClass('d-block');
            }
        } else {
            alertify.alert('Solo se permite amarres en las Cuentas Hijas', function () { }).set({ title: "Error" });

            $('#Amarres').prop('checked', false);
        }
    } else {
        $('#columnaAmarres').removeClass('d-block');
        $('#columnaAmarres').addClass('d-none');
        $('#columnaAmarresVacio').removeClass('d-none');
        $('#columnaAmarresVacio').addClass('d-block');

        $('.clase_amarre').remove();

        if ($('.clase_amarre').length == 0) {
            $('#tabla_amarres > tbody').html('<tr id="tr_vacio_amarre"><td align="center" colspan="5">No hay datos para mostrar</td></tr>');
        }
    }
}

function nuevaFilaAmarre() {
    $('#tr_vacio_amarre').remove();

    var nuevo = `
            <tr id="tr_amarre${id_amarre}" class="clase_amarre">
                <td>
                    <input type="text" name="Items[]" class="Items form-control form-control-sm" value="${id_amarre}" readonly />
                </td>
                <td>
                    <select name="CuentaDebe[]" class="CuentaDebe form-control form-control-sm" id="CuentaDebe${id_amarre}">

                    </select>
                </td>
                <td>
                    <select name="CuentaHaber[]" class="CuentaHaber form-control form-control-sm" id="CuentaHaber${id_amarre}">

                    </select>
                </td>
                <td>
                    <input type="text" name="Porcentaje[]" class="Porcentaje form-control form-control-sm" id="Porcentaje${id_amarre}" oninput="esMayorCero(this)" onkeypress="esNumero(event)" />
                </td>
                <td>
                    <button type="button" class="Buttons btn btn-sm btn-danger shadow-sm" onclick="eliminarFilaAmarre('${id_amarre}')">Eliminar</button>
                </td>
            </tr>
        `;

    $('#tabla_amarres > tbody').append(nuevo);

    autocompletado('#CuentaDebe' + id_amarre, {}, BASE_URL + "app/mantenience/accounting_plan/autocompletado");
    autocompletado('#CuentaHaber' + id_amarre, {}, BASE_URL + "app/mantenience/accounting_plan/autocompletado");

    id_amarre++;
}

function eliminarFilaAmarre(id) {
    $('#tr_amarre' + id).remove();

    $(".clase_amarre").each(function (i) {
        this.id = 'tr_amarre' + (i + 1);
    });

    $(".Items").each(function (i) {
        this.value = i + 1;
    });

    $(".CuentaDebe").each(function (i) {
        this.id = 'CuentaDebe' + (i + 1);
    });

    $(".CuentaHaber").each(function (i) {
        this.id = 'CuentaHaber' + (i + 1);
    });

    $(".Porcentaje").each(function (i) {
        this.id = 'Porcentaje' + (i + 1);
    });

    $(".Buttons").each(function (i) {
        $(this).attr('onclick', 'eliminarFilaAmarre(' + (i + 1) + ')');
    });

    if ($('.clase_amarre').length == 0) {
        $('#tabla_amarres > tbody').append('<tr id="tr_vacio_amarre"><td align="center" colspan="5">No hay datos para mostrar</td></tr>');
    }

    id_amarre = $(".clase_amarre").length + 1;
}

function verificarFormulario() {
    var CodCuenta = $('#CodCuenta').val();
    var CuentaPadre = $('#CuentaPadre').val();
    var DescCuenta = $('#DescCuenta').val();
    var existe_cuenta_hijo = false;
    var existe_cuenta_o_descripcion = false;
    var existe_tipo = '';
    var existe_codigo = '';
    var falta_cuenta_debe = false;
    var falta_cuenta_haber = false;
    var suma_porcentaje = 0;
    var estado_porcentaje = false;
    var array_Cuenta_Debe = $(".CuentaDebe option:selected").toArray().map(item => item.value);
    var array_Cuenta_Haber = $(".CuentaHaber option:selected").toArray().map(item => item.value);
    var array_index = 0;
    var repetir_debe_haber = false;
    var existe_cuenta_debe = false;
    var mensaje_error = '';
    var repite_valor_Debe = false;
    var repite_valor_Haber = false;

    $('.CuentaDebe').removeClass('border-rojo');
    $('.CuentaHaber').removeClass('border-rojo');
    $('.Porcentaje').removeClass('border-rojo');

    if (CodCuenta.length == 0) {
        alertify.alert('Debe Registrar la Cuenta!', function () { }).set({ title: "Error" });

        return false;
    }

    $.ajax({
        'url': BASE_URL + 'app/mantenience/accounting_plan/consulta_cuenta',
        'data': { CodCuenta, DescCuenta, tipo: 'verificar_cuenta_hijo' },
        'type': 'POST',
        'async': false,
        success: function (data) {
            var datos = JSON.parse(data);

            existe_cuenta_o_descripcion = datos.existe;
            existe_tipo = datos.tipo;
            existe_codigo = datos.codigo;
        }
    });

    if (existe_cuenta_o_descripcion) {
        var mensaje = existe_tipo == 'codigo' ? 'Ya Existe una Cuenta con la misma Cuenta' : 'Ya Existe la Descripción en el Plan Contable';

        alertify.alert(mensaje + '<br>Modifique los datos y vuelva a Intentarlo', function () { }).set({ title: "Error" });

        return false;
    }

    if (CodCuenta.length > 2 && CuentaPadre.length == 0) {
        alertify.alert('No tiene cuenta padre!', function () { }).set({ title: "Error" });

        return false;
    }

    if (DescCuenta.length == 0) {
        alertify.alert('Debe Registrar la Descripción!', function () { }).set({ title: "Error" });

        return false;
    }

    if ($('#Amarres').is(':checked')) {
        var index = 0;

        $(".Items").each(function (i) {
            if ($('#CuentaDebe' + (i + 1)).val() == null) {
                index = i + 1;
                falta_cuenta_debe = true;
                return false;
            }

            if ($('#CuentaHaber' + (i + 1)).val() == null) {
                index = i + 1;
                falta_cuenta_haber = true;
                return false;
            }

            if ($('#Porcentaje' + (i + 1)).val().length == 0) {
                index = i + 1;
                estado_porcentaje = true;
                return false;
            }

            if ($('#Porcentaje' + (i + 1)).val().length > 0) {
                index = i + 1;
                suma_porcentaje += parseFloat($('#Porcentaje' + (i + 1)).val().length == 0 ? 0 : $('#Porcentaje' + (i + 1)).val());
            }
        });

        if (falta_cuenta_debe) {
            $('#CuentaDebe' + index).addClass('border-rojo');

            alertify.alert('Falta Registrar la Cuenta del Debe<br>Modifique y Vuelva Intentarlo!!', function () { }).set({ title: "Error" });

            return false;
        }

        if (falta_cuenta_haber) {
            $('#CuentaHaber' + index).addClass('border-rojo');

            alertify.alert('Falta Registrar la Cuenta del Haber<br>Modifique y Vuelva Intentarlo!!', function () { }).set({ title: "Error" });

            return false;
        }

        if (estado_porcentaje) {
            $('#Porcentaje' + index).addClass('border-rojo');

            alertify.alert('Registre el Porcentaje<br>Modifique los datos y vuelva Intentarlo!!', function () { }).set({ title: "Error" });

            return false;
        }

        if ($(".Porcentaje").length > 0 && $(".Porcentaje").length == index && (suma_porcentaje < 100 || suma_porcentaje > 100)) {
            $('.Porcentaje').addClass('border-rojo');

            alertify.alert('La Suma de Los Porcentajes Debe Ser Igual A <<100>>', function () { }).set({ title: "Error" });

            return false;
        }

        index = 0;

        $(".CuentaDebe option:selected").each(function (i) {
            if (this.value == array_Cuenta_Haber[i]) {
                index = i + 1;
                mensaje_error = 'No Se Puede Repetir la Cuenta del Debe con la Cuenta Del Haber';
                repetir_debe_haber = true;
                return false;
            }

            if (array_Cuenta_Haber.includes(this.value)) {
                array_index = array_Cuenta_Haber.indexOf(this.value) + 1;
                index = i + 1;
                mensaje_error = 'La Cuenta del Debe: <<' + this.value + '>> Ya Existe!!<br>Modifique y Vuelva Intentarlo!!';
                existe_cuenta_debe = true;
                return false;
            }
        });

        if (repetir_debe_haber) {
            $('#CuentaDebe' + index).addClass('border-rojo');

            $('#CuentaHaber' + index).addClass('border-rojo');

            alertify.alert(mensaje_error, function () { }).set({ title: "Error" });

            return false;
        }

        if (existe_cuenta_debe) {
            $('#CuentaDebe' + index).addClass('border-rojo');

            $('#CuentaHaber' + array_index).addClass('border-rojo');

            alertify.alert(mensaje_error, function () { }).set({ title: "Error" });

            return false;
        }

        $(".Items").each(function (i) {
            if (array_Cuenta_Debe.filter(x => x == array_Cuenta_Debe[i]).length > 1) {
                mensaje_error = 'La Cuenta del Debe: <<' + array_Cuenta_Debe[i] + '>> Ya Existe!!<br>Modifique y Vuelva Intentarlo!!';
                repite_valor_Debe = true;

                $('.CuentaDebe > select').each(function (j) {
                    if (this.value == array_Cuenta_Debe[i]) {
                        $(this).addClass('border-rojo');
                    }
                });

                return false;
            }

            /* if(array_Cuenta_Haber.filter(x => x == array_Cuenta_Haber[i]).length > 1){
                mensaje_error = 'La Cuenta del Haber: <<' +  array_Cuenta_Haber[i] + '>> Ya Existe!!<br>Modifique y Vuelva Intentarlo!!';
                repite_valor_Haber = true;

                $('.CuentaHaber > select').each(function(j) {
                    if(this.value == array_Cuenta_Haber[i]){
                        $(this).addClass('border-rojo');
                    }
                });

                return false;
            } */
        });

        if (repite_valor_Debe) {
            alertify.alert(mensaje_error, function () { }).set({ title: "Error" });

            return false;
        }

        /* if(repite_valor_Haber){
            alertify.alert(mensaje_error, function(){}).set({ title: "Error" });

            return false;
        } */
    }

    return true;
}

function submit() {
    $('#form').submit();
}