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

    var clase = 'prestamo_equipo';  // la clase que implementa el CRUD para este grid
    var idPager = 'prestamo_equipo-pager';  // la barra de navegación del grid ubicada en la parte inferior

    // las columnas de un grid se definen como un array de objetos con múltiples atributos
    var columnas = [
         {'label': 'Código préstamo', name: 'codigo_prestamo', index: 'codigo_prestamo', width: 100, sortable: true, editrules: {required: true, number: false, minValue: 1},editoptions: {dataInit: asignarAncho}
         },
        {'label': 'Fecha inicio', name: 'fecha_inicio', index: 'fecha_inicio', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1},editoptions: {dataInit: asignarAncho}
        },
    {'label': 'Fecha fin', name: 'fecha_fin', index: 'fecha_fin', width: 100, sortable: true, editable: true,editrules: {required: true, number: false, minValue: 1},editoptions: {dataInit: asignarAncho}
        },
    {'label': 'Id usuario', name: 'id_usuario', index: 'id_usuario', width: 100, sortable: true, editable: true,edittype: 'select', editrules: {required: true, number: false, minValue: 1},
            editoptions: {
                dataUrl: 'controlador/fachada.php?clase=prestamo_equipo&oper=getSelect',
                dataInit: asignarAncho,
                defaultValue: '0'
            }
        },
    {'label': 'Equipo para prestamo', name: 'equipo_para_prestamo', index: 'equipo_para_prestamo', width: 100, sortable: true, editable: true,       edittype: 'select', editrules: {required: true, number: false, minValue: 1},
            editoptions: {
                dataUrl: 'controlador/fachada.php?clase=prestamo_equipo&oper=getSelect2',
                dataInit: asignarAncho,
                defaultValue: '0'
            }
        }
        

    ];


    // inicializa el grid
    var grid = jQuery('#prestamo_equipo-grid').jqGrid({
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
        sortname: 'codigo_prestamo', // <-- OJO pueden ir varias columnas separadas por comas
        sortorder: "asc",
        height: altoGrid,
        width: anchoGrid,
        pager: "#" + idPager,
        viewrecords: true,
        caption: "Prestamo",
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

        if (columna == 'id_externo') {
            if (valor === '0') {
                return [false, "Falta seleccionar identificador usuario externo"];
            }
        }
        if (columna == 'id_usuario') {
            if (valor === '0') {
                return [false, "Falta seleccionar identificador del usuario"];
            }
        }
        return [true, ""];
    }

});// JavaScript Document