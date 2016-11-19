/* 
 * Permite la actualización de la información de órdenes de producción
 * Demostración de las posibilidades más usuales de un elemento jqGrid
 */

$(function () {

    $(window).resize(); // forzar un resize para detectar el ancho del contenedor (ver index.js)
    var anchoGrid = anchoContenedor; // se asigna a una variable local el ancho del contenedor
    var altoGrid = $(window).height() - 350;

    if (altoGrid < 200) {
        altoGrid = 200;
    }

    var clase = 'cronograma';  // la clase que implementa el CRUD para este grid
    var idPager = 'cronograma-pager';  // la barra de navegación del grid ubicada en la parte inferior

    // las columnas de un grid se definen como un array de objetos con múltiples atributos
    var columnas = [
        {'label': 'In-período', name: 'inicio_periodo', index: 'inicio_periodo', width: 110, sortable: true, editable: true, align: "center",
            editrules: {required: true, date: true, custom: true, custom_func: validarOrdenProduccion},
            editoptions: {
                title: 'AAAA-MM-DD',
                dataInit: function (elemento) {
                    $(elemento).datepicker(initDatePicker);
                    $(elemento).width(260);
                }
            }
        },
        {'label': 'Fin-período', name: 'fin_periodo', index: 'fin_periodo', width: 110, sortable: true, editable: true, align: "center",
            editrules: {required: true, date: true, custom: true, custom_func: validarOrdenProduccion},
            editoptions: {
                title: 'AAAA-MM-DD',
                dataInit: function (elemento) {
                    $(elemento).datepicker(initDatePicker);
                    $(elemento).width(260);
                }
            }
        },
        {'label': 'Grupo', name: 'grupo', index: 'grupo', width: 100, sortable: true, editable: true, edittype: "select",
            editrules: {custom: true, custom_func: validarOrdenProduccion},
            editoptions: {
                dataUrl: 'controlador/fachada.php?clase=Grupo&oper=getSelect',
                dataInit: asignarAncho,
                defaultValue: '0'
            }
        },
        {'label': 'Sala', name: 'sala', index: 'sala', width: 100, sortable: true, editable: true, edittype: "select",
            editrules: {custom: true, custom_func: validarOrdenProduccion},
            editoptions: {
                dataUrl: 'controlador/fachada.php?clase=Sala&oper=getSelect',
                dataInit: asignarAncho,
                defaultValue: '0'
            }
        },
        {'label': 'Día', name: 'dia', index: 'dia', width: 100, sortable: true, editable: true, edittype: "select",
            editrules: {custom: true, custom_func: validarOrdenProduccion},
            editoptions: {value: { 0: 'Seleccione un dia', Monday: 'Lunes', Tuesday: 'Martes', Wednesday: 'Miércoles',
                Thursday: 'Jueves', Friday: 'Viernes', Saturday: 'Sábado', Sunday: 'Domingo'}},
        },
        {'label': 'Hora inicio', name: 'inicio_hora', index: 'inicio_hora', width: 100, sortable: true, editable: true,
            editrules: {required: true, number: false, minValue: 1},
            editoptions: {
                title: 'H:i',
                dataInit: function (elemento) {
                    $(elemento).timepicker(initTimePicker);
                    $(elemento).width(100);
                }
            },
        },
        {'label': 'Hora fin', name: 'fin_hora', index: 'fin_hora', width: 100, sortable: true, editable: true,
            editrules: {required: true, number: false, minValue: 1},
            editoptions: {
                title: 'H:i',
                dataInit: function (elemento) {
                    $(elemento).timepicker(initTimePicker);
                    $(elemento).width(100);
                }
            },
        },
    ];

    // inicializa el grid
    var grid = jQuery('#cronograma-grid').jqGrid({
        url: 'controlador/fachada.php',
        datatype: "json",
        mtype: 'POST',
        postData: {
            clase: clase,
            oper: 'select'
        },
        rowNum: 10,
        rowList: [10, 20, 30],
        colModel: columnas,
        autowidth: false,
        shrinkToFit: false,
        sortname: 'inicio_periodo', // <-- OJO pueden ir varias columnas separadas por comas
        sortorder: "asc",
        height: altoGrid,
        width: anchoGrid,
        pager: "#" + idPager,
        viewrecords: true,
        caption: "Cronograma",
        multiselect: false,
        multiboxonly: true,
        hiddengrid: false,
        cellurl: 'controlador/fachada.php?clase=' + clase,
        cellsubmit: 'remote', // enviar cada entrada
        gridComplete: function () {
            // hacer algo...
        },
        loadError: function (jqXHR, textStatus, errorThrown) {
            alert('Error. No se tiene acceso a los datos de órdenes de producción.')
            console.log('textStatus: ' + textStatus);
            console.log(errorThrown);
            console.log(jqXHR.responseText);
        },
        editurl: "controlador/fachada.php?clase=" + clase
    });

    // inicializa los elementos de la barra de navegación del grid
    grid.jqGrid('navGrid', "#" + idPager, {
        refresh: true,
        edit: true,
        add: true,
        del: true,
        view: false,
        search: true,
        closeOnEscape: false
    }, {// edit
        width: 420,
        modal: true,
        afterSubmit: respuestaServidor
    }, {// add
        width: 420,
        modal: true,
        afterSubmit: respuestaServidor
    }, {// del
        width: 335,
        modal: true, // jqModal: true,
        afterSubmit: respuestaServidor
    }, {// búsqueda
        multipleSearch: true,
        multipleGroup: true}, {}
    );

    /**
     * Asigna ancho a un elemento del grid
     * @param {type} elemento El nombre del elemento 
     * @returns {undefined}
     */
    function asignarAncho(elemento) {
        $(elemento).width(260);
    }

    /**
     * Validación personalizada de los campos de un jqGrid
     * @param {type} valor el dato contenido en un campo
     * @param {type} columna nombre con que está etiquetada la columna
     * @returns {Array} un array indicando si la validación fue exitosa o no
     */
    function validarOrdenProduccion(valor, columna) {

        if (columna == 'Grupo') {
            if (valor === '0') {
                return [false, "Falta seleccionar un grupo"];
            }
        }
        if (columna == 'Sala') {
            if (valor === '0') {
                return [false, "Falta seleccionar una sala"];
            }
        }
        if (columna == 'Día') {
            if (valor === '0') {
                return [false, "Falta seleccionar un día"];
            }
        }
        if (columna === 'In-período') {
            var fechaSolicitud = moment($('#inicio_periodo').val(), 'YYYY-MM-DD', true);

            if (!fechaSolicitud.isValid()) {
                return [false, "Ini - Revise que la fecha esté en formato AAAA-MM-DD"];
            }
            // pueden ser necesarias otras validaciones de la fecha de solicitud. Utilizar moment para dichos casos
        }
        if (columna === 'Fin-período') {
            var fechaSolicitud = moment($('#fin_periodo').val(), 'YYYY-MM-DD', true);

            if (!fechaSolicitud.isValid()) {
                return [false, "Fin - Revise que la fecha esté en formato AAAA-MM-DD"];
            }
            // pueden ser necesarias otras validaciones de la fecha de solicitud. Utilizar moment para dichos casos
        }

        return [true, ""];
    }

});


