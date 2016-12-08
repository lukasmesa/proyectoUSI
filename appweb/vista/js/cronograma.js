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
        {'label': 'Inicio periodo', name: 'inicio_periodo', index: 'inicio_periodo', width: 110, sortable: true, editable: true, align: "center",
            editrules: {required: true, dateTime: true, custom: true, custom_func: validarOrdenProduccion},
            editoptions: {
                title: 'AAAA-MM-DD HH:ii',
                dataInit: function (elemento) {
                    $(elemento).datetimepicker(initDatePicker);
                    $(elemento).width(260);
                }
            }
        },
        {'label': 'Fin periodo', name: 'fin_periodo', index: 'fin_periodo', width: 110, sortable: true, editable: true, align: "center",
            editrules: {required: true, dateTime: true, custom: true, custom_func: validarOrdenProduccion},
            editoptions: {
                title: 'AAAA-MM-DD HH:ii',
                dataInit: function (elemento) {
                    $(elemento).datetimepicker(initDatePicker);
                    $(elemento).width(260);
                }
            }
        },
        {'label': 'Tipo de actividad', name: 'tipo', index: 'tipo', width: 100, sortable: true, editable: true, edittype: "select",
            editrules: {custom: true, custom_func: validarOrdenProduccion},
            editoptions: { value: tipoReserva,
                            dataEvents: [
                                  {  type: 'change',
                                     fn: function(e) {
                                        if(this.value==1){
                                            var lista = getElementos({'clase': 'monitor', 'oper': 'getSelectMonitor', 'json': true});
                                        }else if(this.value ==2 || this.value == 3){
                                            var lista = getElementos({'clase': 'docente', 'oper': 'getSelectDocente', 'json': true});
                                        }
                                        $('#usuario').html(lista);
                                     }
                                  }
                               ]
            }
        },
        {'label': 'Descripci&oacute;n de actividad', name: 'descripcion', index: 'descripcion', width: 100, sortable: true, editable: true, edittype: "textarea",
            editrules: {custom: true, custom_func: validarOrdenProduccion},
            editoptions: {
                dataInit: asignarAncho
            }
        },
        {'label': 'Usuario', name: 'usuario', index: 'usuario', width: 100, sortable: true, editable: true, edittype: "select",
            editrules: {custom: true, custom_func: validarOrdenProduccion},
            editoptions: {
                dataUrl: 'controlador/fachada.php?clase=usuario&oper=getSelectUsuario',
                dataInit: asignarAncho
            }
        },
        {'label': 'Sala', name: 'sala', index: 'sala', width: 100, sortable: true, editable: true, edittype: "select",
            editrules: {custom: true, custom_func: validarOrdenProduccion},
            editoptions: {
                dataUrl: 'controlador/fachada.php?clase=sala&oper=getSelectSala',
                dataInit: asignarAncho
            }
        },
        {'label': 'Dia', name: 'dia', index: 'dia', width: 100, sortable: true, editable: true, edittype: "select",
            editrules: {custom: true, custom_func: validarOrdenProduccion},
            editoptions: {value: diasSemana
            }
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
        edit: false,
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
        closeAfterAdd:true,
        beforeSubmit : function(postdata, formid) { 
            if(moment(postdata.fin_periodo).isAfter(postdata.inicio_periodo)){
                return[true,"Success"]; 
            }else{
                return[false,"Fecha inicio debe ser menor a fecha fin."];
            }
        },
        afterSubmit: function(respuestaServidor){
            if(respuestaServidor.responseText){
                $( function() {
                    jQuery("#dialog-message").text(respuestaServidor.responseText);
                    $( "#dialog-message" ).dialog({
                        minWidth: 350,
                        modal: true,
                        buttons: {
                            Ok: function() {
                                $( this ).dialog( "close" );
                            }
                        }
                    });
                } );
            }
        }
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

        if (columna == 'Usuario') {
            if (valor === '0') {
                return [false, "Falta seleccionar un Usuario"];
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
        if (columna == 'Tipo de actividad') {
            if (valor === '0') {
                return [false, "Falta seleccionar un tipo de actividad"];
            }
        }

        return [true, ""];
    }
    $( "#programacion" ).click(function() {
        $( function() {
            jQuery("#default").text("¡UPS! Esta función no está implementada");
            $( "#default" ).dialog();
        } );
    });

});




