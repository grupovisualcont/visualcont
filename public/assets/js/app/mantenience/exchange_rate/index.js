seleccionar(anio_hoy, mes_hoy);

function seleccionar(anio, mes) {
    $(".Periodo").each(function (i) {
        if ($(this).attr('id') == mes) {
            $(this).addClass('background-readonly');
        } else {
            $(this).removeClass('background-readonly');
        }
    });

    $(".text-black-tabla").each(function (i) {
        if ($(this).attr('id').split('a')[1] == mes) {
            $(this).addClass('underline');
        } else {
            $(this).removeClass('underline');
        }
    });

    $('#excel').attr('href', BASE_URL + 'app/mantenience/exchange_rate/excel/' + anio + '/' + mes);
    $('#pdf').attr('href', BASE_URL + 'app/mantenience/exchange_rate/pdf/' + anio + '/' + mes);
}