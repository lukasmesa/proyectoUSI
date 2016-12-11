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

    var clase = 'asignatura';  // la clase que implementa el CRUD para este grid
    var idPager = 'asignatura-pager';  // la barra de navegación del grid ubicada en la parte inferior

    

    var field1,check_function1 = function(value,colname) 
    {
        
        if (colname === "cod_asignatura") {
            field1 = value;
        } 
        
        if(value.length<5){
            return [false, "El codigo de la asignatura debe tener una longitud mayor a 5"];
        }
        else
        {
            return [true];
        }
        
        return [true];
    };

    var field1,check_function2 = function(value,colname) 
    {
        
        if (colname === "nombre_asignatura") {
            field1 = value;
        }       
          
        if(value.length<3){
            return [false, "El nombre de la asignatura debe tener una longitud mayor a 3"];
        }
        else
        {
            return [true];
        }
        
        return [true];
    };

    // las columnas de un grid se definen como un array de objetos con múltiples atributos
    var columnas = [
        {'label': 'C&oacute;digo', name: 'codigo_asignatura', index: 'codigo_asignatura', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1,custom:true,custom_func:check_function1},
            editoptions: {dataInit: asignarAncho}
        },
        {'label': 'Nombre', name: 'nombre_asignatura', index: 'nombre_asignatura', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1,custom:true,custom_func:check_function2},
            editoptions: {dataInit: asignarAncho}
        }
    ];

    // inicializa el grid
    var grid = jQuery('#asignatura-grid').jqGrid({
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
        sortname: 'nombre_asignatura', // <-- OJO pueden ir varias columnas separadas por comas
        sortorder: "asc",
        height: altoGrid,
        width: anchoGrid,
        pager: "#" + idPager,
        viewrecords: true,
        caption: "Asignaturas",
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

        if (columna == 'Cod_Asignaturas') {
            if (valor === '0') {
                return [false, "Falta seleccionar la Asignatura"];
            }
        }
        if (columna == 'Nom_Asignaturas') {
            if (valor === '0') {
                return [false, "Falta seleccionar la Asignatura"];
            }
        }
        return [true, ""];
    }
});