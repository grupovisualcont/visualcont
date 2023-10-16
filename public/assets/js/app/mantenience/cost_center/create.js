$('select').select2({
    width: 'auto', dropdownAutoWidth: true
});

autocompletado('#CodcCostoSuperior', { Estado: 11 }, BASE_URL + "app/mantenience/cost_center/autocompletado");
autocompletado('#Estado', { IdAnexo: 0, TipoAnexo: 1, OtroDato: '' }, BASE_URL + "app/attached/autocompletado");

function setCodigo() {
    var CodcCosto = $('#CodcCostoSuperior option:selected').val();
    var codigo = '';

    $.ajax({
        'url': BASE_URL + 'app/mantenience/cost_center/consulta_codigo',
        'data': { CodcCosto, tipo: 'nuevo' },
        'type': 'POST',
        'async': false,
        success: function (data) {
            var datos = JSON.parse(data);

            codigo = datos.codigo;
        }
    });

    $('#CodcCosto').val(codigo);
}

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