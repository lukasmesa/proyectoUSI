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
        
       
     /*  {'label': 'Código Préstamo', name: 'codigo_prestamo', index: 'codigo_prestamo', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1},
            editoptions: {
                dataInit: asignarAncho,
                defaultValue:function()
                {
                    return jQuery("#prestamo_equipo-grid").jqGrid('getGridParam', 'records') +1;
                }
                
            }
       },*/
       {'label': 'Fecha inicio', name: 'fecha_inicio', index: 'fecha_inicio', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1},
            editoptions: {
                //dataInit: asignarAncho
                dataInit: function (e) {
                    $(e).datetimepicker({});
                }
            }
       },

        {'label': 'Fecha fin', name: 'fecha_fin', index: 'fecha_fin', width: 100, sortable: true, editable: true,editrules: {required: false, number: false, minValue: 1},
            editoptions: {
                //dataInit: asignarAncho
                dataInit: function (e) {
                    $(e).datetimepicker({});
                }
            }
        },
        {'label': 'Usuario', name: 'id_usuario', index: 'id_usuario', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1},edittype:'select',
            editoptions: {
                /*dataUrl: 'controlador/fachada.php?clase=prestamo_equipo&oper=getSelect',
                dataInit: asignarAncho,
                defaultValue: '0'*/
                dataUrl: 'controlador/fachada.php?clase=usuario&oper=getSelectUsuario2',
                dataInit: asignarAncho
            }
        },
        {'label': 'Equipo para pr&eacute;stamo', name: 'fk_equipo', index: 'fk_equipo', width: 100, sortable: true, editable: true,editrules: {required: true, number: false, minValue: 1},edittype:'select',
            editoptions: {
                /*dataUrl: 'controlador/fachada.php?clase=prestamo_equipo&oper=getSelect2',
                dataInit: asignarAncho,
                defaultValue: '0'*/
                value:valoresSelect2()
            }
        },
        {'label': 'Nombre Usuario', name: 'nombre', index: 'nombre', width: 100, sortable: true,  editrules: { number: false       , minValue: 1},
            editoptions: {
                /*dataUrl: 'controlador/fachada.php?clase=nombre&oper=getSelect',
                dataInit: asignarAncho,
                defaultValue: '0'*/
                dataInit: asignarAncho,
            }
        
        }
    ];


    function valoresSelect1(){
        valoresID="";      
        $.ajax({
            type: 'POST',
            url: "controlador/fachada.php?clase=docente&oper=selectIdsDocente",
            data: {},
            success: function(data)
            {
                var datos=jQuery.parseJSON(data);
                console.log(datos);
                var rows = datos['rows'];                
                for(i in rows)
                {
                    var id=rows[i]['id'];
                    var s=id+":"+id+";";
                    valoresID+=s;                
                }                                
            },              
            async:false
        });
        return valoresID.substr(0,(valoresID.length-1)); 
    }

    function valoresSelect2(){
        valoresIDEquipo="";      
        $.ajax({
            type: 'POST',
            url: "controlador/fachada.php?clase=equipos_para_prestamos&oper=select",
            data: {},
            success: function(data)
            {
                var datos=jQuery.parseJSON(data);
                console.log(datos);
                var rows = datos['rows'];                
                for(i in rows)
                {
                    var id=rows[i]['id'];
                    var s=id+":"+id+";";
                    valoresIDEquipo+=s;                
                }                                
            },              
            async:false
        });
        return valoresIDEquipo.substr(0,(valoresIDEquipo.length-1)); 
    }

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
		closeAfterAdd:true,
        beforeSubmit : function(postdata, formid) { 
            if(moment(postdata.fecha_fin).isAfter(postdata.fecha_inicio)){
                return[true,"Success"]; 
            }else{
                return[false,"Fecha y hora inicio debe ser menor a fecha y hora fin."];
            }
        },
        afterSubmit: respuestaServidor
    }, {// add
        width: 420,
        modal: true,
		closeAfterAdd:true,
        beforeSubmit : function(postdata, formid) { 
            if(moment(postdata.fecha_fin).isAfter(postdata.fecha_inicio)){
                return[true,"Success"]; 
            }else{
                return[false,"Fecha y hora inicio debe ser menor a fecha y hora fin."];
            }
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