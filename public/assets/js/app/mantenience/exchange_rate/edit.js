function api_tipo_cambio() {
    var Mes = $('#Mes option:selected').val();
    var Anio = $('#Anio').val();

    $.ajax({
        'url': BASE_URL + 'app/mantenience/exchange_rate/consulta',
        'data': { Mes, Anio },
        'type': 'POST',
        'async': false,
        success: function (data) {
            var datos = JSON.parse(data);

            for (let index = 0; index < datos.length; index++) {
                $('#FechaTipoCambio' + datos[index]['dia']).val(datos[index]['fecha']);
                $('#ValorCompra' + datos[index]['dia']).val(datos[index]['compra']);
                $('#ValorVenta' + datos[index]['dia']).val(datos[index]['venta']);
            }
        }
    });
}

function submit() {
    $('#form').submit();
}