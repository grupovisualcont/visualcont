$('select').select2({
    width: 'auto', dropdownAutoWidth: true
});

function verificarFormulario() {
    var CodcCosto = $('#CodcCosto').val();
    var DesccCosto = $('#DesccCosto').val();

    if (CodcCosto.length > 20) {
        alertify.alert('El Código debe tener hasta máximo 20 caracteres!', function () { }).set({ title: "Error" });

        return false;
    }

    if (DesccCosto.length == 0) {
        alertify.alert('Debe de Registrar el Nombre del Departamento!', function () { }).set({ title: "Error" });

        return false;
    }

    return true;
}

function submit() {
    $('#form').submit();
}