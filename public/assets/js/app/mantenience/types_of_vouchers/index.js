getTipoVoucherDetalles();

function getTipoVoucherDetalles(CodTV) {
    if (!CodTV) {
        if ($(".CodTV").length > 0) {
            CodTV = $(".CodTV").first().attr('id');
        } else {
            $('#tablaDetalles').parent().removeClass('card');
        }
    }

    if (CodTV) {
        $(".CodTV").each(function (i) {
            $(this).removeClass('background-readonly');
            $(this).addClass('text-black-tabla');
        });

        $(".text-black-tabla").each(function (i) {
            $(this).removeClass('underline');
        });

        $.ajax({
            'url': BASE_URL + 'app/mantenience/types_of_vouchers/consulta_detalles',
            'data': { CodTV },
            'type': 'POST',
            success: function (data) {
                $('#' + CodTV).removeClass('text-black-tabla');
                $('#' + CodTV).addClass('background-readonly');
                $('#a' + CodTV).addClass('underline');
                $('#tablaDetalles').html(data);
            }
        });
    }
}