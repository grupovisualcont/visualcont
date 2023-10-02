function verificarFormulario() {
    var descTipoActivo = $('#descTipoActivo').val();
    var existe_descripcion = false;

    if (descTipoActivo.length == 0) {
        alertify.alert('Debe de Registrar el Nombre del Cuenta!', function () { }).set({ title: "Error" });

        return false;
    }

    $.ajax({
        'url': BASE_URL + 'app/mantenience/asset_types/consulta_nombre',
        'data': { descTipoActivo, tipo: 'nuevo' },
        'type': 'POST',
        'async': false,
        success: function (data) {
            var datos = JSON.parse(data);

            existe_descripcion = datos.existe;
        }
    });

    if (existe_descripcion) {
        alertify.alert('Ya Existe un tipo activo fijo registrado con el mismo nombre<br>Modifique los datos y vuelva a Intentarlo', function () { }).set({ title: "Error" });

        return false;
    }

    return true;
}

function submit() {
    $('#form').submit();
}