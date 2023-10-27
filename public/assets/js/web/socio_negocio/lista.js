/*
 * @version 1.0.0
 */

const objSocioNegocio = {};

$(function() {

    objSocioNegocio.eliminar = function() {
        const boton = $(this);
        const id = boton.data('id');
        $.ajax({
            url: BASE_URL + 'web/mantenience/business_partner/destroy/' + id,
            method: 'DELETE',
            data: {},
            dataType: "json",
            contentType: "application/json",
            beforeSend: function () {}
        }).done(function (response) {
            location.href = BASE_URL + 'web/mantenience/business_partner';
        }).fail(function (jqxhr) {
            alertify.alert(resp.message, function () { }).set({ title: "Error" });
        });
    };

});

$(document).ready(function() {

    $(document).on('click', '.opcion-eliminar', objSocioNegocio.eliminar);

});
