/*!
 *
 * @version 1.0.0
 */

var s2SocioNegocio;

$(function () {

    s2SocioNegocio = {
        /**
         * Parametro del filtros
         * formato:
         *  1 = Normal
         */
        parametros: {
            formato: 1,
        },
        /**
         * Configuración en acciones del plugins
         * formato:
         *  1 = Normal
         */
        acciones: {
            botonCrear: 1,
        },
        /**
         * Sirve para tener un valor predefinido. Siempre que el ID sea diferente de 0
         */
        selected: {id: 0, text: ""},
        /**
         * Sirve para valida que solo se mande 1 sola vez los datos del cliente seleccionado
         * 0: No seleccionado
         * 1: Seleccionado
         */
        validate: 0,
        /**
         * Inicializa la liberia Select2
         * lo hace siempre que se llame el metodo para que previamente haya sido configurado
         */
        init: function (objDOM, dropdownParent, width) {
            objDOM = (typeof objDOM == "undefined") ? $('select#socio_negocio') : objDOM;
            dropdownParent = (typeof dropdownParent == "undefined" || !dropdownParent) ? null : dropdownParent;
            width = (typeof width == "undefined" || !width) ? '100%' : width;

            /**
             * Realiza la busqueda de un socio de negocio
             */
            var select2 = objDOM.select2({
                ajax: {
                    url: BASE_URL + 'app/mantenience/business_partner/autocompletado',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term,
                            parameters: s2SocioNegocio.parametros,
                            acciones: s2SocioNegocio.acciones
                        };
                    },
                    processResults: function (data) {
                        return {results: data};
                    },
                    cache: true
                },
                escapeMarkup: function (markup) {
                    return markup;
                }, // let our custom formatter work
                minimumInputLength: 0,
                // Aqi se muestran todos los resultados de la busqueda
                templateResult: function (res) {
                    // Se inicializa en 0 para poder usar la funcion __seleccionar_socioNegocio
                    s2SocioNegocio.selected.id = 0;
                    s2SocioNegocio.validate = 0;
                    if (typeof res.id !== "undefined") {
                        var text = res.text;
                        // La operacion tendra otro tipo de vista
                        if (res.id == 'C' && s2SocioNegocio.acciones.botonCrear) {
                            text = '<a class="select2-options" ><i class="fa fa-plus" ></i> <b>' + res.text + '</b></a>';
                        }
                        return text;
                    } else {
                        return res.text;
                    }
                },
                templateSelection: function (res) {
                    if (res.id !== "") {
                        // Solo se ejecuta cuando se realiza una busqueda
                        if (s2SocioNegocio.selected.id == 0) {
                            // Solo cuando aun no es selccionado se marca.
                            if (s2SocioNegocio.validate == 0) {
                                if (res.id == "C") {
                                    // objArticleModal.openCreate(res.text, select2);
                                } else {
                                    if (typeof __selectProduct == "function") {
                                        __selectSocioNegocio(res);
                                    }
                                }
                            }
                        }
                        // Se cambia el estado para no voler a seleccionar
                        s2SocioNegocio.validate = 1;
                        return res.text;
                    } else {
                        return 'Elegir';
                    }
                },
                placeholder: "Search",
                allowClear: true,
                width: width,
                dropdownParent: dropdownParent,
            });

            return objDOM;
        },
        /**
         * Activa el plugin Select2 cuando el articulo se encuentra en el detalle
         * @param {number} position
         * @return {Object}
         */
        activeSelect2: function (position) {
            if (!document.getElementById('socio_negocio[' + position + ']')) {
                alert('Error al activar el Pluging Select2');
            } else {
                var combo = $(document.getElementById('socio_negocio[' + position + ']'));
                return this.init(combo);
            }
        }
    };

});

$(document).ready(function () {

});
