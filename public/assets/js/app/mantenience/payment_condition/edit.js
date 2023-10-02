$('select').select2({
    width: 'auto', dropdownAutoWidth: true
});

function verificarFormulario() {
    var desccondpago = $('#desccondpago').val();
    var Tipo = $('#Tipo option:selected').val();
    var Ndias = $('#Ndias').val();

    if (desccondpago.length == 0) {
        alertify.alert('Debe de Registrar el Nombre del Condición de Pago!', function () { }).set({ title: "Error" });

        return false;
    }

    if (Tipo == '168' && (Ndias.length == 0 || parseInt(Ndias) == 0)) {
        alertify.alert('Debe de Registrar los dias de la Condición de Pago!', function () { }).set({ title: "Error" });

        return false;
    }

    return true;
}

function submit() {
    $('#form').submit();
}