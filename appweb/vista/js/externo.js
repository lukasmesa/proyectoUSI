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

    var clase = 'externo';  // la clase que implementa el CRUD para este grid
    var idPager = 'externo-pager';  // la barra de navegación del grid ubicada en la parte inferior

    var field1, check_function1 = function (value, colname)
    {

        if (colname === "nombre") {
            field1 = value;
        }

        if (value.length < 3) {
            console.log("t", value, colname);
            return [false, "El nombre de usuario tiene que ser un como minimo de 3 caracteres"];
        } else
        {
            return [true];
        }

        return [true];
    };

    var field1, check_function22 = function (value, colname)
    {

        if (colname === "apellido") {
            field1 = value;
        }

        if (value.length < 3) {
            console.log("t", value, colname);
            return [false, "El apellido de usuario tiene que ser un aaa como minimo de 3 caracteres"];
        } else
        {
            return [true];
        }

        return [true];
    };
    var field1, check_function3 = function (value, colname)
    {

        if (colname === "correo") {
            field1 = value;
        }

        expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (!expr.test(value)) {
            return [false, "correo invalido"];
        }
        return [true];
    };
    var field1, check_function4 = function (value, colname)
    {

        if (colname === "contrasena") {
            field1 = value;
        }

        if (value.length < 6) {
            return [false, "La contraseña debe ser mayor a 6 caracteres"];
        }
        return [true];
    };
    // las columnas de un grid se definen como un array de objetos con múltiples atributos
    var columnas = [
        {'label': 'Id Externo', name: 'id_usuario', index: 'id_usuario', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1},
            editoptions: {dataInit: asignarAncho}
        },
        {'label': 'Tipo Documento', name: 'tipo_doc', index: 'tipo_doc', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1},edittype:'select',
            editoptions: {
                dataInit: asignarAncho,
                value:tipoDoc
            }
        },
        {'label': 'Nombre', name: 'nombre', index: 'nombre', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1, custom: true, custom_func: check_function1},
            editoptions: {dataInit: asignarAncho}
        },
        {'label': 'Apellido', name: 'apellido', index: 'apellido', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1, custom: true, custom_func: check_function22},
            editoptions: {dataInit: asignarAncho}
        },
        {'label': 'Correo', name: 'correo', index: 'correo', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1, custom: true, custom_func: check_function3},
            editoptions: {dataInit: asignarAncho}
        },
        {'label': 'contrase&ntilde;a', name: 'contrasena', index: 'contrasena', width: 100, sortable: true, editable: true,hidden:true, editrules: {required: true,edithidden:true, number: false, minValue: 1, custom: true, custom_func: check_function4},edittype:'password',
            editoptions: {dataInit: asignarAncho}
        }
    ];

    function valoresSelect(){

        
        valores = "0:cedula;1:codigo";
        return valores;
    }
    // inicializa el grid
    var grid = jQuery('#externo-grid').jqGrid({
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
        sortname: 'id_externo', // <-- OJO pueden ir varias columnas separadas por comas
        sortorder: "asc",
        height: altoGrid,
        width: anchoGrid,
        pager: "#" + idPager,
        viewrecords: true,
        caption: "Externo",
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
		beforeSubmit: function(postdata) {
            postdata['contrasena'] = $.md5(postdata['contrasena'])
            return [true];
        },
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
});