$('select').select2({
    width: 'auto', dropdownAutoWidth: true
});

function setNumeroDocumento() {
    var Serie = $('#Serie').val().toUpperCase();
    var Numero = $('#Numero').val();
    var NumeroDocumento = '';

    if (Serie.length > 0 && Numero.length > 0) {
        if (Serie.length >= 3) {
            NumeroDocumento += Serie;

            switch (Numero.length) {
                case 1:
                    NumeroDocumento += '-000000' + Numero;
                    break;
                case 2:
                    NumeroDocumento += '-00000' + Numero;
                    break;
                case 3:
                    NumeroDocumento += '-0000' + Numero;
                    break;
                case 4:
                    NumeroDocumento += '-000' + Numero;
                    break;
                case 5:
                    NumeroDocumento += '-00' + Numero;
                    break;
                case 6:
                    NumeroDocumento += '-0' + Numero;
                    break;
                case 7:
                    NumeroDocumento += '-' + Numero;
                    break;
            }

            $('#NumeroDocumento').val(NumeroDocumento);
        } else {
            $('#NumeroDocumento').val('');
        }
    } else {
        $('#NumeroDocumento').val('');
    }
}

function verificarFormulario() {
    var CodDocumento = $('#CodDocumento').val();
    var DescDocumento = $('#DescDocumento').val();
    var Serie = $('#Serie').val();
    var Numero = $('#Numero').val();
    var CodSunat = $('#CodSunat').val();
    var existe_codigo_documento = false;

    if (CodDocumento.length == 0) {
        alertify.alert('Debe de Registrar el Código del Documento!', function () { }).set({ title: "Error" });

        return false;
    }

    $.ajax({
        'url': BASE_URL + 'app/mantenience/payment_vouchers/consulta_codigo',
        'data': { CodDocumento, tipo: 'nuevo' },
        'type': 'POST',
        'async': false,
        success: function (data) {
            var datos = JSON.parse(data);

            existe_codigo_documento = datos.existe;
        }
    });

    if (existe_codigo_documento) {
        alertify.alert('No puede registrar el Documento porque ya se esta utilizando.<br>Modifique el Código de Documento e intentelo nuevamente', function () { }).set({ title: "Error" });

        return false;
    }

    if (DescDocumento.length == 0) {
        alertify.alert('Debe de Registrar el Nombre del Documento!', function () { }).set({ title: "Error" });

        return false;
    }

    if (Serie.length == 0) {
        alertify.alert('Debe de Registrar la Serie del Documento!', function () { }).set({ title: "Error" });

        return false;
    }

    if (Serie.length < 4) {
        alertify.alert('La serie debe tener una longitud de 4 caracteres', function () { }).set({ title: "Error" });

        return false;
    }

    if (Numero.length == 0) {
        alertify.alert('Debe de Registrar el Número Secuencial del Documento!', function () { }).set({ title: "Error" });

        return false;
    }

    if (CodSunat.length == 0) {
        alertify.alert('Debe de Registrar Código de Sunat!', function () { }).set({ title: "Error" });

        return false;
    }

    return true;
}

function submit() {
    $('#form').submit();
}