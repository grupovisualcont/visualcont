$('select').select2({
    width: 'auto', dropdownAutoWidth: true
});

autocompletado($('#CodEntidad'), { }, BASE_URL + "app/entidadFinanciera/autocompletado");
autocompletado($('#CodMoneda'), { }, BASE_URL + "app/moneda/autocompletado_");
autocompletado($('#codcuenta'), { }, BASE_URL + "app/mantenience/accounting_plan/autocompletado");

cambiar_estado_checkbox('Propio');

function cambiar_estado_checkbox(id) {
    if ($('#' + id).is(':checked')) {
        $('#' + id).val('1');
    } else {
        $('#' + id).val('0');
    }

    if ($('#Propio').is(':checked')) {
        $('#codcuenta').attr('disabled', false);
        $('#CodMoneda').attr('disabled', false);
        $('#ctacte').attr('disabled', false);
    } else if (!$('#Propio').is(':checked')) {
        $('#codcuenta').attr('disabled', true);
        $('#CodMoneda').attr('disabled', true);
        $('#ctacte').attr('disabled', true);
    }
}

function agregar() {
    $('#tr_vacio_cheque').remove();

    var nuevo = `
            <tr id="tr_cheque${id_cheque}" class="clase_cheque">
                <td>
                    <input type="text" name="DescCheque[]" class="DescCheque form-control form-control-sm" id="DescCheque${id_cheque}" />
                </td>
                <td>
                    <input type="text" name="nroinicial[]" class="nroinicial form-control form-control-sm" id="nroinicial${id_cheque}" oninput="esMayorCero(this)" onkeypress="esNumero(event)" />
                </td>
                <td>
                    <input type="text" name="nrOfinal[]" class="nrOfinal form-control form-control-sm" id="nrOfinal${id_cheque}" oninput="esMayorCero(this)" onkeypress="esNumero(event)" />
                </td>
                <td>
                    <input type="text" name="numerador[]" class="numerador form-control form-control-sm" id="numerador${id_cheque}" oninput="esMayorCero(this)" onkeypress="esNumero(event)" />
                </td>
                <td align="center">
                    <button type="button" class="Buttons btn btn-sm btn-danger shadow-sm" onclick="eliminar(${id_cheque})">Eliminar</button>
                </td>
            </tr>
        `;

    $('#tabla_cheque > tbody').append(nuevo);

    id_cheque++;
}

function eliminar(id) {
    $('#tr_cheque' + id).remove();

    $(".clase_cheque").each(function (i) {
        this.id = 'tr_cheque' + (i + 1);
    });

    $(".DescCheque").each(function (i) {
        this.id = 'DescCheque' + (i + 1);
    });

    $(".nroinicial").each(function (i) {
        this.id = 'nroinicial' + (i + 1);
    });

    $(".nrOfinal").each(function (i) {
        this.id = 'nrOfinal' + (i + 1);
    });

    $(".numerador").each(function (i) {
        this.id = 'numerador' + (i + 1);
    });

    $(".Buttons").each(function (i) {
        $(this).attr('onclick', 'eliminar(' + (i + 1) + ')');
    });

    if ($('.clase_cheque').length == 0) {
        $('#tabla_cheque > tbody').append('<tr id="tr_vacio_cheque"><td align="center" colspan="5">No hay datos para mostrar</td></tr>');
    }

    id_cheque = $(".clase_cheque").length + 1;
}

function verificarFormulario() {
    var CodEntidad = $('#CodEntidad option:selected').val();
    var abreviatura = $('#abreviatura').val();
    var existe_descripcion = false;
    var existe_numero_inicial = false;
    var existe_numero_final = false;
    var existe_numerador = false;
    var numero_final_es_menor = false;
    var numerador_entre_inicial_final = false;

    $('.DescCheque').removeClass('border-rojo');
    $('.nroinicial').removeClass('border-rojo');
    $('.nrOfinal').removeClass('border-rojo');
    $('.numerador').removeClass('border-rojo');

    if (CodEntidad == null) {
        alertify.alert('Debe seleccionar el Banco!', function () { }).set({ title: "Error" });

        return false;
    }

    if (abreviatura.length == 0) {
        alertify.alert('Debe ingresar la abreviatura!', function () { }).set({ title: "Error" });

        return false;
    }

    if ($('.clase_cheque').length > 0) {
        var index = 0;

        $(".DescCheque").each(function (i) {
            if (this.value.length == 0) {
                index = i + 1;
                existe_descripcion = true;
                return false;
            }

            if ($('#nroinicial' + (i + 1)).val().length == 0) {
                index = i + 1;
                existe_numero_inicial = true;
                return false;
            }

            if ($('#nrOfinal' + (i + 1)).val().length == 0) {
                index = i + 1;
                existe_numero_final = true;
                return false;
            } else if (parseFloat($('#nrOfinal' + (i + 1)).val()) < parseFloat($('#nroinicial' + (i + 1)).val())) {
                index = i + 1;
                numero_final_es_menor = true;
                return false;
            }

            if ($('#numerador' + (i + 1)).val().length == 0) {
                index = i + 1;
                existe_numerador = true;
                return false;
            } else if (parseFloat($('#numerador' + (i + 1)).val()) < parseFloat($('#nroinicial' + (i + 1)).val()) ||
                parseFloat($('#numerador' + (i + 1)).val()) > parseFloat($('#nrOfinal' + (i + 1)).val())) {
                index = i + 1;
                numerador_entre_inicial_final = true;
                return false;
            }
        });

        if (existe_descripcion) {
            $('#DescCheque' + index).addClass('border-rojo');

            alertify.alert('Debe ingresar la Descripción!', function () { }).set({ title: "Error" });

            return false;
        }

        if (existe_numero_inicial) {
            $('#nroinicial' + index).addClass('border-rojo');

            alertify.alert('Debe ingresar el número inicial!', function () { }).set({ title: "Error" });

            return false;
        }

        if (numero_final_es_menor) {
            $('#nrOfinal' + index).addClass('border-rojo');

            alertify.alert('El número final no puede ser menor al numero inicial', function () { }).set({ title: "Error" });

            return false;
        }

        if (existe_numero_final) {
            $('#nrOfinal' + index).addClass('border-rojo');

            alertify.alert('Debe ingresar el número final!', function () { }).set({ title: "Error" });

            return false;
        }

        if (existe_numerador) {
            $('#numerador' + index).addClass('border-rojo');

            alertify.alert('Debe ingresar el numerador!', function () { }).set({ title: "Error" });

            return false;
        }

        if (numerador_entre_inicial_final) {
            $('#numerador' + index).addClass('border-rojo');

            alertify.alert('El numerador debe estar entre el número inicial y final', function () { }).set({ title: "Error" });

            return false;
        }
    }

    return true;
}

function submit() {
    $('#form').submit();
}