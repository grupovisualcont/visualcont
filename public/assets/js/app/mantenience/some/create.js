$('select').select2({
    width: 'auto', dropdownAutoWidth: true
});

autocompletado($('.CodInterno'), { }, BASE_URL + "app/mantenience/accounting_plan/autocompletado");
autocompletado($('.Estado'), { IdAnexo: 0, TipoAnexo: 1, OtroDato: '', Value: 'CodInterno' }, BASE_URL + "app/attached/autocompletado");

function cambiarPorTipo() {
    var Tipo = parseInt($('#Tipo option:selected').val());

    $('.Tipo').removeClass('display-block');
    $('.Tipo').addClass('display-none');

    $('#Tipo' + Tipo).removeClass('display-none');
    $('#Tipo' + Tipo).addClass('display-block');
}

function verificarFormulario() {
    var Tipo = parseInt($('#Tipo option:selected').val());
    var Descripcion = $('#DescAnexo' + Tipo).val();

    if (Descripcion.length == 0) {
        alertify.alert('Debe de Registrar la Descripción!', function () { }).set({ title: "Error" });

        return false;
    }

    return true;
}

function submit() {
    $('#form').submit();
}