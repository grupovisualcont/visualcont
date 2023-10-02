$(document).ready(function () {
    var table = $('#dataTablePlanContable').DataTable({
        language: {
            "decimal": "",
            "emptyTable": "No hay información",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
            "infoEmpty": "Mostrando 0 a 0 de 0 Entradas",
            "infoFiltered": "(Filtrado de _MAX_ total entradas)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ Entradas",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "Sin resultados encontrados",
            "paginate": {
                "first": "Primero",
                "last": "Ultimo",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
        "serverSide": true,
        "serverMethod": 'POST',
        "ajax": BASE_URL + 'app/mantenience/accounting_plan/datos',
        "columnDefs": [{
            "className": 'text-center',
            targets: [5],
            "render": function (data, type, row, meta) {
                var td = `
                        <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-bars"></i>
                        </button>
                        <div class="dropdown-menu" x-placement="left-start">
                            <a class="dropdown-item" href="${BASE_URL}app/mantenience/accounting_plan/edit/${row[0]}/${row[5]}">
                                <i class="fa fa-edit"></i> Editar
                            </a>
                            <a class="dropdown-item" href="${BASE_URL}app/mantenience/accounting_plan/delete/${row[0]}/${row[5]}" onclick="return confirm('¿Está seguro de eliminar?')">
                                <i class="fa fa-trash"></i> Eliminar
                            </a>
                        </div>
                    `;

                return td;
            },
        }],
        order: [[1, 'asc']],
        targets: 'no-sort',
        bSort: false,
    });

    $('input[type = search]').on('keyup', function (e) {
        table.column(0).search(this.value, true, true, false).draw();
    });
});