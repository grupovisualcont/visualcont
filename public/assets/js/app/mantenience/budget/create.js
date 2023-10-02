$('select').select2({
    width: 'auto', dropdownAutoWidth: true
});

autocompletado($('#CodCuenta'), BASE_URL + "app/mantenience/accounting_plan/autocompletado");

function cambiarPorNivel() {
    var Nivel = (parseInt($('#Tipo option:selected').val()) + 1);

    $('.Nivel').removeClass('display-block');
    $('.Nivel').addClass('display-none');

    $('#Nivel' + Nivel).removeClass('display-none');
    $('#Nivel' + Nivel).addClass('display-block');
}

function get_options_nivel_2() {
    var Nivel = (parseInt($('#Tipo option:selected').val()) + 1);
    var Niveles1 = $('#Niveles1-' + Nivel + ' option:selected') ? $('#Niveles1-' + Nivel + ' option:selected').val() : '';
    var estado = false;

    $.ajax({
        'url': BASE_URL + 'app/mantenience/budget/consulta_codigo',
        'data': { Niveles1, tipo: 'options_nivel_2' },
        'type': 'POST',
        'async': false,
        success: function (data) {
            estado = true;
            $('#Niveles2-' + Nivel).html(data);
            $('#Niveles2-' + Nivel).select2({
                width: 'auto', dropdownAutoWidth: true
            });
        }
    });

    if (estado) {
        setCodigo();
    }
}

function get_options_nivel_3() {
    var Nivel = (parseInt($('#Tipo option:selected').val()) + 1);
    var Niveles2 = $('#Niveles2-' + Nivel + ' option:selected') ? $('#Niveles2-' + Nivel + ' option:selected').val() : '';
    var estado = false;

    $.ajax({
        'url': BASE_URL + 'app/mantenience/budget/consulta_codigo',
        'data': { Niveles2, tipo: 'options_nivel_3' },
        'type': 'POST',
        'async': false,
        success: function (data) {
            estado = true;
            $('#Niveles3-' + Nivel).html(data);
            $('#Niveles3-' + Nivel).select2({
                width: 'auto', dropdownAutoWidth: true
            });
        }
    });

    if (estado) {
        setCodigo();
    }
}

function setCodigo() {
    var Nivel = (parseInt($('#Tipo option:selected').val()) + 1);
    var Niveles1 = $('#Niveles1-' + Nivel + ' option:selected') ? $('#Niveles1-' + Nivel + ' option:selected').val() : '';
    var Niveles2 = $('#Niveles2-' + Nivel + ' option:selected') ? $('#Niveles2-' + Nivel + ' option:selected').val() : '';
    var Niveles3 = $('#Niveles3-' + Nivel + ' option:selected') ? $('#Niveles3-' + Nivel + ' option:selected').val() : '';
    var codigo = '';

    $.ajax({
        'url': BASE_URL + 'app/mantenience/budget/consulta_codigo',
        'data': { Nivel, Niveles1, Niveles2, Niveles3, tipo: 'nuevo' },
        'type': 'POST',
        'async': false,
        success: function (data) {
            var datos = JSON.parse(data);

            codigo = datos.codigo;
        }
    });

    $('#Codigo' + Nivel).val(codigo);

    if ($('#Niveles1-' + Nivel + ' option:selected').val() != '') {
        $('#Niveles2-' + Nivel).removeAttr('disabled');
        $('#Niveles2-' + Nivel).select2({
            width: 'auto', dropdownAutoWidth: true
        });
    }

    if ($('#Niveles2-' + Nivel + ' option:selected').val() != '') {
        $('#Niveles3-' + Nivel).removeAttr('disabled');
        $('#Niveles3-' + Nivel).select2({
            width: 'auto', dropdownAutoWidth: true
        });
    }
}

function verificarFormulario() {
    var Nivel = (parseInt($('#Tipo option:selected').val()) + 1);
    var Niveles1 = $('#Niveles1-' + Nivel + ' option:selected').val();
    var Niveles2 = $('#Niveles2-' + Nivel + ' option:selected').val();
    var Niveles3 = $('#Niveles3-' + Nivel + ' option:selected').val();
    var Descripcion = $('#Descripcion' + Nivel).val();

    switch (Nivel) {
        case 2:
            if (Niveles1.length == 0) {
                alertify.alert('Debe de Seleccionar la Categoría Principal!', function () { }).set({ title: "Error" });

                return false;
            }

            break;
        case 3:
            if (Niveles1.length == 0) {
                alertify.alert('Debe de Seleccionar la Categoría Principal!', function () { }).set({ title: "Error" });

                return false;
            }

            if (Niveles2.length == 0) {
                alertify.alert('Debe de Seleccionar la Categoría 2!', function () { }).set({ title: "Error" });

                return false;
            }

            break;
        case 4:
            if (Niveles1.length == 0) {
                alertify.alert('Debe de Seleccionar la Categoría Principal!', function () { }).set({ title: "Error" });

                return false;
            }

            if (Niveles2.length == 0) {
                alertify.alert('Debe de Seleccionar la Categoría 2!', function () { }).set({ title: "Error" });

                return false;
            }

            if (Niveles3.length == 0) {
                alertify.alert('Debe de Seleccionar la Categoría 3!', function () { }).set({ title: "Error" });

                return false;
            }

            break;
    }

    if (Descripcion.length == 0) {
        alertify.alert('Debe de Registrar el Nombre del Concepto!', function () { }).set({ title: "Error" });

        return false;
    }

    return true;
}

function submit() {
    $('#form').submit();
}