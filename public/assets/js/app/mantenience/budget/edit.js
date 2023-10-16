autocompletado('#CodCuenta', { }, BASE_URL + "app/mantenience/accounting_plan/autocompletado");

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