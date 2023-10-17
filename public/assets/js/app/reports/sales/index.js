$('select.form-control').select2();

function cambiar_checkbox(id){
    if($('#formato').is(':checked')){
        $('#' + id).attr('action', BASE_URL + 'app/reports/sales/registro_ventas_sunat_formato_14_1/pdf');
    }else{
        $('#' + id).attr('action', BASE_URL + 'app/reports/sales/registro_ventas_sunat/pdf');
    }
}

function submit_excel(id){
    $('#' + id).removeAttr('target');
    var url = $('#' + id).attr('action').replace('pdf', 'excel');
    $('#' + id).attr('action', url);
    $('#' + id).submit();
}

function submit_pdf(id){
    $('#' + id).attr('target', '_blank');
    var url = $('#' + id).attr('action').replace('excel', 'pdf');
    $('#' + id).attr('action', url);
    $('#' + id).submit();
}