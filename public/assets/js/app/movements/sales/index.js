getMovimientoDet()

function getMovimientoDet(IdMov) {
    if (!IdMov) {
        if ($(".IdMov").length > 0) {
            IdMov = $(".IdMov").first().attr('id');
        } else {
            $('#tablaDetalles').parent().removeClass('card');
        }
    }

    if (IdMov) {
        $(".IdMov").each(function (i) {
            $(this).removeClass('background-readonly');
            $(this).addClass('text-black-tabla');
        });

        $(".text-black-tabla").each(function (i) {
            $(this).removeClass('underline');
        });

        $('#excel').attr('href', BASE_URL + 'app/movements/sales/excel/' + IdMov);
        $('#pdf').attr('href', BASE_URL + 'app/movements/sales/pdf/' + IdMov);

        $.ajax({
            'url': BASE_URL + 'app/movements/sales/consulta_detalles_index',
            'data': { IdMov },
            'type': 'POST',
            success: function (data) {
                $('#' + IdMov).removeClass('text-black-tabla');
                $('#' + IdMov).addClass('background-readonly');
                $('#a' + IdMov).addClass('underline');
                $('#tablaDetalles').html(data);
            }
        });
    }
}

function set_IdMov_datos(IdMov) {
    $.ajax({
        'url': BASE_URL + 'app/movements/sales/consulta_detalles_PA',
        'data': { IdMov },
        'type': 'POST',
        success: function (data) {
            var datos = JSON.parse(data);

            $('#PAModalLabel').html(datos.titulo);
            $('#PAModalBody').html(datos.tabla);
        }
    });
}