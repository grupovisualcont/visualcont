autocompletado('.Cuentas', { App: 'Ventas' }, BASE_URL + 'app/mantenience/accounting_plan/autocompletado');
autocompletado('.Documentos', { App: 'Ventas' }, BASE_URL + 'app/documento/autocompletado');

var split = 0;

if (window.innerWidth > 768) {
    $('.split').removeClass('row');
    $('#split-0').removeClass('col-md-12');
    $('#split-1').removeClass('col-md-12');

    split = Split(['#split-0', '#split-1'], {
        sizes: [70, 30],
        direction: 'horizontal',
    });

    $('#tabla_importar').removeClass('display-none');
    $('#tabla_importar_totales').removeClass('display-none');
    $('#Opciones').removeClass('display-none');
    $('#Cuentas').removeClass('display-none');
} else {
    $('.split').addClass('row');
    $('#split-0').addClass('col-md-12');
    $('#split-1').addClass('col-md-12');

    $('#tabla_importar').removeClass('display-none');
    $('#tabla_importar_totales').removeClass('display-none');
    $('#Opciones').removeClass('display-none');
    $('#Cuentas').removeClass('display-none');
}

async function pegar_celdas() {
    const text = await navigator.clipboard.readText();

    if (text.trim() != '') {
        var name = $('#tabla_importar > thead th').map(function(index) {
            if (index >= 1) return this.innerText;
        });

        var columnas = text.split('\n');

        // if (documentos.includes(columnas[0].split('\t')[0]) || $.inArray(columnas[0].split('\t')[0], name) > -1) {
        $('#tr_vacio_importar').remove();
        $('#tabla_importar > tbody').html('');

        var tr = '';

        var head = columnas[0].split('\t');

        if ($.inArray(head[0], name) > -1) {
            columnas.shift();
        }

        if (columnas[columnas.length - 1] == '') columnas.pop();

        var index = 1;

        $.each(columnas, function(i) {
            var values = this.split('\t');

            tr += '<tr class="tr_celdas">';
            tr += '<td class="background-importar text-white text-center"><input type="hidden" name="NumItem[]" class="NumItem" value="' + index + '" />' + index + '</td>';

            index++;

            $.each(name, function(j) {
                if (!values[j]) values[j] = '';

                tr += '<td class="text-center"><input type="hidden" name="' + this.trim() + '[]" value="' + values[j].trim() + '" class="' + this.trim() + '" />' + values[j].trim() + '</td>';
            });

            tr += '<tr>';
        });

        $('#tabla_importar > tbody').html(tr);

        sumar_totales();
        // }
    }
}

function traer_xml(evt) {
    if (!evt.target.files) {
        return;
    }

    var name = $('#tabla_importar > thead th').map(function(index) {
        if (index >= 1) return this.innerText;
    });

    var tr = '';

    var index = 1;

    for (const file of evt.target.files) {
        if (file.type == 'text/xml') {
            var path = (window.URL || window.webkitURL).createObjectURL(file);

            $.get(
                path,
                function(data) {
                    var $xml = $(data);
                    var $Invoice = $xml.find('Invoice');

                    if ($Invoice) {
                        $Invoice.each(function() {
                            var CodSunat = $(this).find('cbc\\:InvoiceTypeCode').html();

                            var CodDoc = '';

                            if ($('#TC' + CodSunat)) {
                                if ($('#Documento' + $('#TC' + CodSunat).val() + ' option:selected')) {
                                    CodDoc = $('#Documento' + $('#TC' + CodSunat).val() + ' option:selected').val();
                                }
                            }

                            var Comprobante = $(this).find('cbc\\:ID').html();
                            var SerieDoc = '';
                            var NroDocDel = '';

                            if (Comprobante && Comprobante.includes('-')) {
                                Comprobante = Comprobante.split('-');

                                SerieDoc = Comprobante[0];
                                NroDocDel = Comprobante[1];
                            }

                            var Ruc_Clie = $(this).find('cac\\:AccountingCustomerParty').find('cac\\:Party').find('cac\\:PartyIdentification').find('cbc\\:ID').html();

                            if (!Ruc_Clie) Ruc_Clie = '';

                            var Fecha = $(this).find('cbc\\:IssueDate').html();
                            var Fec_Vcmto = '';

                            if (Fecha) {
                                var date = new Date(Fecha);

                                Fecha = date.toLocaleDateString('en-GB');

                                Fec_Vcmto = Fecha;
                            } else {
                                Fecha = '';
                            }

                            var Moneda = $(this).find('cbc\\:DocumentCurrencyCode').html();

                            if (Moneda) {
                                Moneda = $.map(monedas, function(item) {
                                    if (
                                        item.CodMoneda.toUpperCase() == Moneda.toUpperCase() ||
                                        item.DescMoneda.toUpperCase() == Moneda.toUpperCase() ||
                                        item.AbrevMoneda.toUpperCase() == Moneda.toUpperCase()
                                    )
                                        return item.CodMoneda;
                                });

                                if (Moneda.length == 0) Moneda = '';
                            } else {
                                Moneda = '';
                            }

                            var Neto = $(this).find('cac\\:TaxTotal').find('cac\\:TaxSubtotal').find('cbc\\:TaxableAmount').html();

                            if (!Neto) Neto = 0;

                            var Igv = $(this).find('cac\\:TaxTotal').find('cac\\:TaxSubtotal').find('cbc\\:TaxAmount').html();

                            if (!Igv) Igv = 0;

                            var Otros_Trib = $(this).find('cac\\:LegalMonetaryTotal').find('cbc\\:ChargeTotalAmount').html();

                            if (!Otros_Trib) Otros_Trib = 0;

                            var Total = $(this).find('cac\\:LegalMonetaryTotal').find('cbc\\:PayableAmount').html();

                            if (!Total) Total = 0;

                            var Glosa = $(this).find('cac\\:InvoiceLine').find('cac\\:Item').find('cbc\\:Description').html();

                            if (Glosa) {
                                Glosa = Glosa.replace('<![CDATA[', '');
                                Glosa = Glosa.replace(']]>', '');
                            } else {
                                Glosa = '';
                            }

                            var values = [{
                                    name: 'CodDoc',
                                    value: CodDoc,
                                },
                                {
                                    name: 'SerieDoc',
                                    value: SerieDoc,
                                },
                                {
                                    name: 'NroDocDel',
                                    value: NroDocDel,
                                },
                                {
                                    name: 'Cond_Pago',
                                    value: 'CREDITO',
                                },
                                {
                                    name: 'Ruc_Clie',
                                    value: Ruc_Clie,
                                },
                                {
                                    name: 'Fecha',
                                    value: Fecha,
                                },
                                {
                                    name: 'Fec_Vcmto',
                                    value: Fec_Vcmto,
                                },
                                {
                                    name: 'Moneda',
                                    value: Moneda,
                                },
                                {
                                    name: 'Neto',
                                    value: Neto,
                                },
                                {
                                    name: 'Igv',
                                    value: Igv,
                                },
                                {
                                    name: 'Otros_Trib',
                                    value: Otros_Trib,
                                },
                                {
                                    name: 'Total',
                                    value: Total,
                                },
                                {
                                    name: 'Glosa',
                                    value: Glosa,
                                },
                            ];

                            tr += '<tr class="tr_celdas">';
                            tr += '<td class="background-importar text-white text-center"><input type="hidden" name="NumItem[]" class="NumItem" value="' + index + '" />' + index + '</td>';

                            index++;

                            $.each(name, function(j) {
                                var name_auxiliar = this.trim();

                                var td_class = name_auxiliar.toLowerCase() == 'CodDoc'.toLowerCase() ? 'td_CodDoc' : '';
                                var td_align = name_auxiliar.toLowerCase() == 'Glosa'.toLowerCase() ? 'text-left' : 'text-center';

                                var item = $.grep(values, function(i) {
                                    return i.name == name_auxiliar;
                                });

                                var value = '';

                                if (item.length > 0) {
                                    value = item[0].value;
                                }

                                tr += '<td class="' + td_align + ' ' + td_class + '"><input type="hidden" name="' + name_auxiliar + '[]" class="' + name_auxiliar + '" value="' + value + '" data-CodSunat="' + CodSunat + '" />' + value + '</td>';
                            });

                            tr += '</tr>';
                        });

                        $('#tabla_importar > tbody').html(tr);

                        sumar_totales();
                    }
                },
                'xml'
            );
        }
    }
}

function sumar_totales() {
    var Reg = 0;
    var Afecto = 0;
    var Inafecto = 0;
    var Exonerado = 0;
    var ICBP = 0;
    var Igv = 0;
    var Otros_Trib = 0;
    var Total = 0;

    $('.NumItem').each(function(i) {
        Reg = (i + 1);
    });

    $('.Neto').each(function(i) {
        Afecto += !isNaN(parseFloat($(this).val().trim().replace(',', ''))) ? parseFloat($(this).val().trim().replace(',', '')) : 0;
    });

    $('.Inafecto').each(function(i) {
        Inafecto += !isNaN(parseFloat($(this).val().trim().replace(',', ''))) ? parseFloat($(this).val().trim().replace(',', '')) : 0;
    });

    $('.Exonerado').each(function(i) {
        Exonerado += !isNaN(parseFloat($(this).val().trim().replace(',', ''))) ? parseFloat($(this).val().trim().replace(',', '')) : 0;
    });

    $('.ICBP').each(function(i) {
        ICBP += !isNaN(parseFloat($(this).val().trim().replace(',', ''))) ? parseFloat($(this).val().trim().replace(',', '')) : 0;
    });

    $('.Igv').each(function(i) {
        Igv += !isNaN(parseFloat($(this).val().trim().replace(',', ''))) ? parseFloat($(this).val().trim().replace(',', '')) : 0;
    });

    $('.Otros_Trib').each(function(i) {
        Otros_Trib += !isNaN(parseFloat($(this).val().trim().replace(',', ''))) ? parseFloat($(this).val().trim().replace(',', '')) : 0;
    });

    $('.Total').each(function(i) {
        Total += !isNaN(parseFloat($(this).val().trim().replace(',', ''))) ? parseFloat($(this).val().trim().replace(',', '')) : 0;
    });

    $('#Reg_Total').html(Reg);
    $('#Afecto_Total').html(Afecto.toFixed(2));
    $('#Inafecto_Total').html(Inafecto.toFixed(2));
    $('#Exonerado_Total').html(Exonerado.toFixed(2));
    $('#ICBP_Total').html(ICBP.toFixed(2));
    $('#Igv_Total').html(Igv.toFixed(2));
    $('#Otros_Trib_Total').html(Otros_Trib.toFixed(2));
    $('#Total_Total').html(Total.toFixed(2));
}

function descargar_estructura() {
    alertify.confirm('Confirmar: Desea crear la estructura de Ventas!', function(e) {
        if (e) {
            var a = document.createElement('a');
            a.href = BASE_URL + 'assets/excel/For_Ventas.xlsm';
            a.click();
        }
    }).set({ title: 'Contabilidad' }).set('labels', { ok: 'Si', cancel: 'No' });
}

function limpiar() {
    $('#tabla_importar > tbody').html(
        '<tr id="tr_vacio_importar"><td align="center" colspan="41">No hay datos para mostrar</td></tr>'
    );

    $('#xml').val(null);

    sumar_totales();

    // $('#Cuentas').removeClass('display-none');
    // $('#Cuentas').addClass('display-block');
    // $('#Observaciones').removeClass('display-block');
    // $('#Observaciones').addClass('display-none');

    // if (split) {
    //         split.setSizes([70, 30]);
    // }
}

function historial() {
    $('#btnEliminarHistorial').removeAttr('onclick');

    $.ajax({
        url: BASE_URL + 'app/movements/sales/historial_importar',
        data: {
            tipo: 'nuevo',
            subtipo: 'consulta',
        },
        type: 'POST',
        async: false,
        success: function(data) {
            var datos = JSON.parse(data);

            if (datos.estado) {
                $('#tabla_historial > tbody').html(datos.tr);
            } else {
                $('#tabla_historial > tbody').html('<tr><td align="center" colspan="1">No hay datos para mostrar</td></tr>');
            }
        },
    });
}

function set_historial(id) {
    $('.tr_historial td').removeClass('bg-primary text-white');

    $('#tr_historial_' + id + ' td').addClass('bg-primary text-white');

    $('#btnEliminarHistorial').attr('onclick', 'eliminar_historial(' + id + ')');
}

function eliminar_historial(id) {
    alertify.confirm('¿Está seguro de eliminar?', function(e) {
        if (e) {
            $.ajax({
                url: BASE_URL + 'app/movements/sales/historial_importar',
                data: {
                    id,
                    tipo: 'nuevo',
                    subtipo: 'eliminar',
                },
                type: 'POST',
                async: false,
                success: function(data) {
                    var datos = JSON.parse(data);

                    if (datos.tr) {
                        $('#tabla_historial > tbody').html(datos.tr);
                    } else {
                        $('#tabla_historial > tbody').html('<tr><td align="center" colspan="1">No hay datos para mostrar</td></tr>');
                    }

                    $('#btnEliminarHistorial').removeAttr('onclick');

                    if (datos.estado) {
                        $('#historialModal').modal('hide');

                        alertify.success('Correcto. Datos actualizados exitosamente');
                    } else {
                        alertify.error('Error. Consulte con el administrador');
                    }
                },
            });
        }
    }).set({ title: 'Contabilidad' }).set('labels', { ok: 'Si', cancel: 'No' });
}

function cambiar_opcion() {
    if ($('#opcionRadioCuentas').is(':checked')) {
        $('#Cuentas').removeClass('display-none');
        $('#Observaciones').addClass('display-none');
    } else if ($('#opcionRadioObservaciones').is(':checked')) {
        $('#Cuentas').addClass('display-none');
        $('#Observaciones').removeClass('display-none');
    }
}

function cambiar_documento(CodSunat) {
    var Documento = $('#Documento' + CodSunat + ' option:selected').val();

    $('.td_CodDoc').each(function(i) {
        if ($(this).children('input').attr('data-CodSunat') == CodSunat) {
            $(this).html('<input type="hidden" name="CodDoc[]" value="' + Documento + '" data-CodSunat="' + CodSunat + '" />' + Documento);
        }
    });
}

function agregar_ruc(...numero_documentos) {
    var estado_error = false;

    $.ajax({
        url: BASE_URL + 'app/movements/sales/importar_registrar_socio_negocio',
        data: {
            numero_documentos,
        },
        type: 'POST',
        success: function(data) {
            var datos = JSON.parse(data);
            var mensaje = '<ul>';

            for (let index = 0; index < datos.length; index++) {
                if (datos[index].error) {
                    estado_error = true;

                    mensaje += '<li class="py-2"><span class="bg-danger text-white p-2">' + datos[index].error + '</span></li>';
                    // alertify.error(datos[index].error);
                }

                if (datos[index].success) {
                    mensaje += '<li class="py-2"><span class="bg-primary text-white p-2">' + datos[index].success + '</span></li>';

                    // alertify.success(datos[index].success);

                    numero_documentos = numero_documentos.filter(
                        (item) => item != datos[index].documento
                    );

                    if (numero_documentos.length == 0) {
                        $('#btnAgregarRuc').removeAttr('onclick');
                        $('#btnAgregarRuc').addClass('display-none');
                    } else {
                        $('#btnAgregarRuc').attr(
                            'onclick',
                            'agregar_ruc(' + numero_documentos + ')'
                        );
                    }

                    $('.Ruc_Clie_Existe').each(function(i) {
                        if (this.innerHTML == datos[index].documento) {
                            $(this).parent().remove();
                        }
                    });
                }
            }

            mensaje += '</ul>';

            if ($('#tabla_observaciones tbody tr').length == 0) {
                $('#tabla_observaciones tbody').html('<tr><td align="center" colspan="3">No hay datos para mostrar</td></tr>');
            }

            if (estado_error) {
                alertify.alert(mensaje, function() {}).set({ title: 'Contabilidad' });
            } else {
                alertify.alert('Se registro correctamente, vuelva a importar', function() {}).set({ title: 'Contabilidad' });
            }
        },
    });
}

var contador_size = 0;

function verificarFormulario() {
    var Neto_Cuenta = $('#Neto_Cuenta option:selected').val();
    var Inafecto_Cuenta = $('#Inafecto_Cuenta option:selected').val();
    var Exonerado_Cuenta = $('#Exonerado_Cuenta option:selected').val();
    var Igv_Cuenta = $('#Igv_Cuenta option:selected').val();
    var Icbp_Cuenta = $('#Icbp_Cuenta option:selected').val();
    var Descuento_Cuenta = $('#Descuento_Cuenta option:selected').val();
    var Otro_Tributo_Cuenta = $('#Otro_Tributo_Cuenta option:selected').val();
    var TotalS_Cuenta = $('#TotalS_Cuenta option:selected').val();
    var TotalD_Cuenta = $('#TotalD_Cuenta option:selected').val();
    var Caja_Cuenta = $('#Caja_Cuenta option:selected').val();

    if (Neto_Cuenta.length == 0) {
        $('#Neto_Cuenta').focus();

        alertify.alert('Ingrese CodCuenta para Neto', function() {}).set({ title: 'Contabilidad' });
    } else if (Inafecto_Cuenta.length == 0) {
        $('#Inafecto_Cuenta').focus();

        alertify.alert('Ingrese CodCuenta para Inafecto', function() {}).set({ title: 'Contabilidad' });
    } else if (Exonerado_Cuenta.length == 0) {
        $('#Exonerado_Cuenta').focus();

        alertify.alert('Ingrese CodCuenta para Exonerado', function() {}).set({ title: 'Contabilidad' });
    } else if (Igv_Cuenta.length == 0) {
        $('#Igv_Cuenta').focus();

        alertify.alert('Ingrese CodCuenta para Igv', function() {}).set({ title: 'Contabilidad' });
    } else if (Icbp_Cuenta.length == 0) {
        $('#Icbp_Cuenta').focus();

        alertify.alert('Ingrese CodCuenta para Icbp', function() {}).set({ title: 'Contabilidad' });
    } else if (Descuento_Cuenta.length == 0) {
        $('#Descuento_Cuenta').focus();

        alertify.alert('Ingrese CodCuenta para Descuento', function() {}).set({ title: 'Contabilidad' });
    } else if (Otro_Tributo_Cuenta.length == 0) {
        $('#Otro_Tributo_Cuenta').focus();

        alertify.alert('Ingrese CodCuenta para Otro Tributo', function() {}).set({ title: 'Contabilidad' });
    } else if (TotalS_Cuenta.length == 0) {
        $('#TotalS_Cuenta').focus();

        alertify.alert('Ingrese CodCuenta para Total Soles', function() {}).set({ title: 'Contabilidad' });
    } else if (TotalD_Cuenta.length == 0) {
        $('#TotalD_Cuenta').focus();

        alertify.alert('Ingrese CodCuenta para Total Dolares', function() {}).set({ title: 'Contabilidad' });
    } else if (Caja_Cuenta.length == 0) {
        $('#Caja_Cuenta').focus();

        alertify.alert('Ingrese CodCuenta para Caja', function() {}).set({ title: 'Contabilidad' });
    } else if ($('.tr_celdas').length == 0) {
        alertify.alert('No hay información', function() {}).set({ title: 'Contabilidad' });
    } else {
        var form = $('#form').serialize();

        $.ajax({
            url: BASE_URL + 'app/movements/sales/consulta_importar',
            data: {
                form,
                tipo: 'nuevo',
            },
            type: 'POST',
            async: false,
            success: function(data) {
                console.log(data);
                var datos = JSON.parse(data);

                var auto = '';

                if (datos.rucs && datos.rucs.length > 0) {
                    auto = '<div class="mb-3"><button type="button" class="btn btn-sm btn-primary" id="btnAgregarRuc" onclick="agregar_ruc(' + datos.rucs + ')">Auto</button></div>';
                }

                if (datos.estado_observacion) {
                    var table = '<table class="table table-sm table-bordered" id="tabla_observaciones"><thead class="background-observacion text-white"><tr><th>Descripción</th><th></th><th>Tipo</th></tr></thead>';
                    table += '<tbody>';
                    table += datos.observaciones;
                    table += '</tbody>';
                    table += '</table>';

                    $('#opcionRadioCuentas').attr('checked', false);
                    $('#opcionRadioObservaciones').attr('checked', true);

                    $('#Cuentas').addClass('display-none');
                    $('#Observaciones').removeClass('display-none');

                    $('#Observaciones').html(auto + table);

                    if (split) {
                        split.setSizes([50, 50]);
                    }

                    alertify.alert('Revisar las Observaciones', function() {}).set({ title: 'Contabilidad' });
                } else if (datos.estado) {
                    alertify.alert('Se importo correctamente', function() {}).set({ title: 'Contabilidad' }).set({ invokeOnCloseOff: true, onok: () => location.reload() });
                } else {
                    alertify.alert('Error en la importacion', function() {}).set({ title: 'Contabilidad' });
                }
            },
        });
    }
}