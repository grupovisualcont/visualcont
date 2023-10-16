$('select').select2({
    width: 'auto', dropdownAutoWidth: true
});

autocompletado('#codTipoActivo', {}, BASE_URL + "app/mantenience/asset_types/autocompletado");
autocompletado('#depresiacion', { IdAnexo: 0, TipoAnexo: 15, OtroDato: '' }, BASE_URL + "app/attached/autocompletado");
autocompletado('#CtaCtableGasto', { tipo: 'gasto' }, BASE_URL + "app/mantenience/accounting_plan/autocompletado");
autocompletado('#CtaCtableDepreciacion', { tipo: 'depresiacion' }, BASE_URL + "app/mantenience/accounting_plan/autocompletado");
autocompletado('#estado', { IdAnexo: 0, TipoAnexo: 1, OtroDato: '' }, BASE_URL + "app/attached/autocompletado");
autocompletado('#CodCcosto', { }, BASE_URL + "app/mantenience/cost_center/autocompletado");
autocompletado('#codubigeo', { tipo: 'ubigeo' }, BASE_URL + "app/ubigeo/autocompletado");
autocompletado('#IdCatalogo', { IdAnexoS: 0, TipoAnexoS: 13 }, BASE_URL + "app/iAnexoSunat/autocompletado");
autocompletado('#IdTipoActivo', { IdAnexoS: 0, TipoAnexoS: 18 }, BASE_URL + "app/iAnexoSunat/autocompletado");
autocompletado('#IdEstadoActivo', { IdAnexoS: 0, TipoAnexoS: 19 }, BASE_URL + "app/iAnexoSunat/autocompletado");
autocompletado('#IdMetodo', { IdAnexoS: 0, TipoAnexoS: 20 }, BASE_URL + "app/iAnexoSunat/autocompletado");

function verificarFormulario() {
    var descripcion = $('#descripcion').val();
    var fechaAdqui = $('#fechaAdqui').val();
    var fechaInicio = $('#fechaInicio').val();
    var existe_descripcion = false;

    if (descripcion.length == 0) {
        alertify.alert('Debe de Registrar el Nombre del Activo Fijo!', function () { }).set({ title: "Error" });

        return false;
    }

    $.ajax({
        'url': BASE_URL + 'app/mantenience/fixed_assets/consulta_nombre',
        'data': { descripcion, Notdescripcion: activo_fijo_descripcion, tipo: 'editar' },
        'type': 'POST',
        'async': false,
        success: function (data) {
            var datos = JSON.parse(data);

            existe_descripcion = datos.existe;
        }
    });

    if (existe_descripcion) {
        alertify.alert('Ya Existe un activo fijo registrado con el mismo nombre<br>Modifique los datos y vuelva a Intentarlo', function () { }).set({ title: "Error" });

        return false;
    }

    if (fechaAdqui.length == 0) {
        alertify.alert('Debe registrar fecha de adquisición!', function () { }).set({ title: "Error" });

        return false;
    }

    if (fechaInicio.length == 0) {
        alertify.alert('Debe registrar fecha de Inicio!', function () { }).set({ title: "Error" });

        return false;
    }

    return true;
}

function submit() {
    $('#form').submit();
}