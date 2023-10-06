$('select').select2({
    width: 'auto', dropdownAutoWidth: true
});

autocompletado($('#CodTipPer'), {}, BASE_URL + "app/type_person/autocompletado");
autocompletado($('#CodTipoDoc'), { tipo: 'documento'}, BASE_URL + "app/identity_document_type/autocompletado");
autocompletado($('#IdCondicion'), { IdAnexo: 0, TipoAnexo: 2, OtroDato: '' }, BASE_URL + "app/attached/autocompletado");
autocompletado($('#Idestado'), { IdAnexo: 0, TipoAnexo: 1, OtroDato: '' }, BASE_URL + "app/attached/autocompletado");
autocompletado($('#IdSexo'), { IdAnexo: 0, TipoAnexo: 3, OtroDato: '' }, BASE_URL + "app/attached/autocompletado");
autocompletado($('#CodTipoDoc_Tele'), { tipo: 'banco' }, BASE_URL + "app/identity_document_type/autocompletado");
autocompletado($('#CodVinculo'), {}, BASE_URL + "app/ts27Vinculo/autocompletado");
autocompletado($('#pais'), { tipo: 'pais' }, BASE_URL + "app/ubigeo/autocompletado");
autocompletado($('#select_codubigeo'), { tipo: 'ubigeo' }, BASE_URL + "app/ubigeo/autocompletado");

verificarTipoDocumentoIdentidad();

function cambiarInputByPais() {
    var pais = $('#pais').val();

    if (pais.length == 2) {
        $('#input_codubigeo').addClass('display-none');
        $('#input_codubigeo').removeClass('display-block');
        $('#select_codubigeo').select2().next().show();
    } else {
        $('#input_codubigeo').addClass('display-block');
        $('#input_codubigeo').removeClass('display-none');
        $('#select_codubigeo').select2().next().hide();
    }
}

function verificarLongitudDocumento(item) {
    var CodTipoDoc = $('#CodTipoDoc option:selected').val();

    if (CodTipoDoc == '-') {
        $('#ruc').val('');
        $('#docidentidad').val('');
    } else {
        if (item.id == 'ruc') {
            $('#docidentidad').val('');

            if (item.value.length > 11) {
                alertify.alert('Debe Ingresar hasta 11 dígitos!', function () { }).set({ title: "Error" });
            }

            item.value = item.value.substr(0, 11);
        } else if (item.id == 'docidentidad') {
            $('#ruc').val('');

            if (item.value.length > 8) {
                alertify.alert('Debe Ingresar hasta 8 dígitos!', function () { }).set({ title: "Error" });
            }

            item.value = item.value.substr(0, 8);
        }
    }
}

function consulta_sunat(tipo_documento) {
    var numero_documento = '';

    if (tipo_documento == 'ruc') {
        numero_documento = $('#ruc').val();
    } else {
        numero_documento = $('#docidentidad').val();
    }

    if (numero_documento.length == 8 || numero_documento.length == 11) {
        $.ajax({
            'url': BASE_URL + 'empresa/consulta_sunat',
            'data': { tipo_documento, numero_documento },
            'type': 'POST',
            success: function (data) {
                var datos = JSON.parse(data);

                if (datos.error == null) {
                    if (tipo_documento == 'ruc') {
                        nuevo_option('#CodTipPer',  { CodTipPer: '02' }, BASE_URL + "app/type_person/autocompletado");

                        $('#docidentidad').val('');
                    } else {
                        nuevo_option('#CodTipPer', { CodTipPer: '01' }, BASE_URL + "app/type_person/autocompletado");

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

                    nuevo_option('#CodTipoDoc', { tipo: 'documento', CodTipoDoc: datos.tipoDocumento }, BASE_URL + "app/identity_document_type/autocompletado");

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
                        $('#IdCondicion').val(condicion);
                    }

                    $('#direccion1').val(datos.direccion);

                    var estados = $('#Idestado')[0];

                    var estado = '';

                    for (let index = 0; index < estados.length; index++) {
                        if (estados[index].attributes[1].value.toLowerCase() == datos.estado.toLowerCase()) {
                            estado = estados[index].value;
                            index = estados.length;
                        }
                    }

                    if (estado.length > 0) {
                        $('#Idestado').val(estado);
                    }

                    verificarTipoDocumentoIdentidad();
                } else {
                    alertify.error('Número de documento inválido');
                }
            }
        });
    }
}

function verificarTipoDocumentoIdentidad() {
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
                $('#' + id).attr('oninput', 'verificarLongitudDocumento(this)');
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

function nuevoBanco() {
    $('#tr_empty').remove();

    var nuevo = `
            <tr id="tr_banco${id_banco}" class="clase_banco">
                <td>
                    <select name="CodBanco[]" class="CodBanco form-control form-control-sm" id="CodBanco${id_banco}">

                    </select>
                </td>
                <td>
                    <select name="idTipoCuenta[]" class="idTipoCuenta form-control form-control-sm" id="idTipoCuenta${id_banco}">

                    </select>
                </td>
                <td>
                    <input type="text" name="NroCuenta[]" class="form-control form-control-sm" onkeypress="esNumero(event)" />
                </td>
                <td>
                    <input type="text" name="NroCuentaCCI[]" class="form-control form-control-sm" onkeypress="esNumero(event)" />
                </td>
                <td>
                    <input type="radio" name="Predeterminado" id="Predeterminado${id_banco}" class="Predeterminado" value="0" onchange="cambiarPredeterminado('${id_banco}')" />
                </td>
                <td></td>
                <td>
                    <button type="button" class="Buttons btn btn-sm btn-danger shadow-sm" onclick="eliminar('${id_banco}')">Eliminar</button>
                </td>
            </tr>
        `;

    $('#tablaBanco > tbody').append(nuevo);

    autocompletado($('#CodBanco' + id_banco), {}, BASE_URL + "app/mantenience/box_banks/autocompletado");
    autocompletado($('#idTipoCuenta' + id_banco), { IdAnexo: 0, TipoAnexo: 54, OtroDato: '02' }, BASE_URL + "app/attached/autocompletado");


    id_banco++;
}

function cambiarPredeterminado(id) {
    $(".Predeterminado").each(function (i) {
        this.value = '0';

        if ($(this).is(':checked')) {
            this.value = i + 1;
        }
    });
}

function eliminar(id) {
    $('#tr_banco' + id).remove();

    $(".clase_banco").each(function (i) {
        this.id = 'tr_banco' + (i + 1);
    });

    $(".CodBanco").each(function (i) {
        this.id = 'CodBanco' + (i + 1);
        $(this).select2({
            width: 'auto', dropdownAutoWidth: true
        });
    });

    $(".idTipoCuenta").each(function (i) {
        this.id = 'idTipoCuenta' + (i + 1);
        $(this).select2({
            width: 'auto', dropdownAutoWidth: true
        });
    });

    $(".Predeterminado").each(function (i) {
        this.id = 'Predeterminado' + (i + 1);

        this.value = i + 1;

        $(this).attr('onchange', 'cambiarPredeterminado(' + (i + 1) + ')');
    });

    $(".Buttons").each(function (i) {
        $(this).attr('onclick', 'eliminar(' + (i + 1) + ')');
    });

    if ($('.clase_banco').length == 0) {
        $('#tablaBanco > tbody').append('<tr id="tr_empty"><td align="center" colspan="7">No hay datos para mostrar</td></tr>');
    }

    id_banco = $(".clase_banco").length + 1;
}

function verificarFormulario() {
    var ApePat = $('#ApePat').val();
    var ApeMat = $('#ApeMat').val();
    var Nom1 = $('#Nom1').val();
    var CodTipPer = $('#CodTipPer').val();
    var CodTipoDoc = $('#CodTipoDoc').val();
    var TipoDato = CodTipoDoc != null ? $('#CodTipoDoc option:selected').attr('data-tipo-dato').split('|') : '';
    var falta_codigo_banco = false;
    var falta_tipo_cuenta = false;
    var existe_duplicados = false;
    var existe_codigo = '';

    $('.CodBanco').removeClass('border-rojo');
    $('.idTipoCuenta').removeClass('border-rojo');

    if (CodTipPer == null) {
        alertify.alert('Debe Ingresar Tipo Persona!', function () { }).set({ title: "Error" });

        return false;
    }

    if (CodTipoDoc == null) {
        alertify.alert('Debe Ingresar Tipo Documento!', function () { }).set({ title: "Error" });

        return false;
    }

    if (CodTipPer == datos_ruc_CodTipPer && CodTipoDoc == datos_ruc_CodTipoDoc) {
        $('#docidentidad').val('');
    } else {
        $('#ruc').val('');
    }

    if ((CodTipPer != datos_ruc_CodTipPer && CodTipoDoc == datos_ruc_CodTipoDoc) || (CodTipPer == datos_ruc_CodTipPer && CodTipoDoc != datos_ruc_CodTipoDoc)) {
        alertify.alert('Debe de Seleccionar el Tipo de Persona y el Tipo de Documento de Identidad correctos!', function () { }).set({ title: "Error" });

        return false;
    }

    var ruc = $('#ruc').val();
    var docidentidad = $('#docidentidad').val();
    var razonsocial = $('#razonsocial').val();
    var pais = $('#pais option:selected').val();
    var select_codubigeo = $('#select_codubigeo').val();
    var input_codubigeo = $('#input_codubigeo').val();

    if (ApePat.length == 0 && (CodTipPer != datos_ruc_CodTipPer && (CodTipPer != datos_extranjero_CodTipPer && CodTipoDoc != datos_extranjero_CodTipoDoc))) {
        alertify.alert('Debe de Registrar el A. Paterno del Cliente!', function () { }).set({ title: "Error" });

        return false;
    }

    if (ApeMat.length == 0 && (CodTipPer != datos_ruc_CodTipPer && (CodTipPer != datos_extranjero_CodTipPer && CodTipoDoc != datos_extranjero_CodTipoDoc))) {
        alertify.alert('Debe de Registrar el A. Materno del Cliente!', function () { }).set({ title: "Error" });

        return false;
    }

    if (Nom1.length == 0 && (CodTipPer != datos_ruc_CodTipPer && (CodTipPer != datos_extranjero_CodTipPer && CodTipoDoc != datos_extranjero_CodTipoDoc))) {
        alertify.alert('Debe de Registrar el Nombre del Cliente!', function () { }).set({ title: "Error" });

        return false;
    }

    if (razonsocial.length == 0 && (CodTipPer == datos_ruc_CodTipPer || (CodTipPer == datos_extranjero_CodTipPer && CodTipoDoc == datos_extranjero_CodTipoDoc))) {
        alertify.alert('Debe de Registrar la Razón Social!', function () { }).set({ title: "Error" });

        return false;
    }

    if (docidentidad.length == 0 && CodTipPer != datos_ruc_CodTipPer && CodTipoDoc != datos_extranjero_CodTipoDoc) {
        alertify.alert('Debe Ingresar DNI!', function () { }).set({ title: "Error" });

        return false;
    }

    if (docidentidad.length == 0 && CodTipoDoc != datos_ruc_CodTipoDoc && CodTipoDoc != datos_extranjero_CodTipoDoc) {
        alertify.alert('Debe Ingresar DNI!', function () { }).set({ title: "Error" });

        return false;
    }

    if (ruc.length == 0 && CodTipPer == datos_ruc_CodTipPer && CodTipoDoc != datos_extranjero_CodTipoDoc) {
        alertify.alert('Debe Ingresar RUC!', function () { }).set({ title: "Error" });

        return false;
    }

    if (ruc.length == 0 && CodTipoDoc == datos_ruc_CodTipoDoc && CodTipoDoc != datos_extranjero_CodTipoDoc) {
        alertify.alert('Debe Ingresar RUC!', function () { }).set({ title: "Error" });

        return false;
    }

    $.ajax({
        'url': BASE_URL + 'app/mantenience/business_partner/consulta_duplicados',
        'data': { tipo: 'editar', ruc, docidentidad, razonsocial, Notruc: socio_negocio_ruc, Notdocidentidad: socio_negocio_docidentidad, Notrazonsocial: socio_negocio_razonsocial },
        'type': 'POST',
        'async': false,
        success: function (data) {
            var datos = JSON.parse(data);

            existe_duplicados = datos.existe;
            existe_codigo = datos.codigo;
        }
    });

    if (existe_duplicados) {
        alertify.alert('Ya existe el N° de Documento: ' + existe_codigo, function () { }).set({ title: "Error" });

        return false;
    }

    if (TipoDato.length == 3) {
        var longitud = parseInt(TipoDato[0]);
        var tipo_dato = TipoDato[1];
        var tipo_longitud = TipoDato[2];

        if (docidentidad.length > 0 && docidentidad.length < longitud && tipo_longitud == datos_ruc_N_tip && CodTipPer != datos_ruc_CodTipPer) {
            alertify.alert('Debe Ingresar hasta ' + longitud + ' dígitos!', function () { });

            return false;
        }

        if (ruc.length > 0 && ruc.length < longitud && tipo_longitud == datos_ruc_N_tip && CodTipPer == datos_ruc_CodTipPer) {
            alertify.alert('Debe Ingresar hasta ' + longitud + ' dígitos!', function () { });

            return false;
        }
    }

    if ((pais.length == 2 && select_codubigeo && select_codubigeo.length == 0) || (pais.length != 2 && input_codubigeo && input_codubigeo.length == 0)) {
        alertify.alert('Debe Ingresar Ubigeo!', function () { }).set({ title: "Error" });

        return false;
    }

    if ($(".clase_banco").length > 0) {
        var index = 0;

        $(".CodBanco > select").each(function (i) {
            if ($(this).val().length == 0) {
                index = i + 1;
                falta_codigo_banco = true;
                return false;
            }

            if ($('#idTipoCuenta' + (i + 1)).val().length == 0) {
                index = i + 1;
                falta_tipo_cuenta = true;
                return false;
            }
        });

        if (falta_codigo_banco) {
            $('#CodBanco' + index).addClass('border-rojo');
            $('#CodBanco' + index).select2({
                width: 'auto', dropdownAutoWidth: true
            });

            alertify.alert('Falta Seleccionar la Entidad Financiera<br>Modifique y Vuelva Intentarlo!!', function () { }).set({ title: "Error" });

            return false;
        }

        if (falta_tipo_cuenta) {
            $('#idTipoCuenta' + index).addClass('border-rojo');
            $('#idTipoCuenta' + index).select2({
                width: 'auto', dropdownAutoWidth: true
            });

            alertify.alert('Falta Seleccionar el Tipo de Cuenta<br>Modifique y Vuelva Intentarlo!!', function () { }).set({ title: "Error" });

            return false;
        }
    }

    return true;
}

function submit() {
    $('#form').submit();
}