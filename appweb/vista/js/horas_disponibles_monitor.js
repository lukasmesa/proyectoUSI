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

    valoresSelect3();

    var clase = 'horas_disponibles_monitor';  // la clase que implementa el CRUD para este grid
    var idPager = 'horas_disponibles_monitor-pager';  // la barra de navegación del grid ubicada en la parte inferior

     var field1, check_function1 = function (value, colname)
    {

        if (colname === "hora_inicio") {
            field1 = value;
        }

        if (isNaN(value)|| value<=6 || value >=21) {
            return [false, "El campo debe tener un numero entre 6 y 21"];
        }

        return [true];
    };

    var field1, check_function2 = function (value, colname)
    {

        if (colname === "hora_fin") {
            field1 = value;
        }

        if (isNaN(value)|| value<=6 || value >=21) {
           
            return [false, "El campo debe tener un numero entre 6 y 21"];
        }
        return [true];
    };

    // las columnas de un grid se definen como un array de objetos con múltiples atributos
    var columnas = [
        /*{'label': 'Id Horario Monitor', name: 'id_horario', index: 'id_horario', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1},
            editoptions: {
                dataInit: asignarAncho,
                defaultValue:function()
                {
                    return jQuery("#horas_disponibles_monitor-grid").jqGrid('getGridParam', 'records') +1;
                }
            }
        },*/
        {'label': 'Día', name: 'dia', index: 'dia', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1},edittype:'select',
            editoptions: {defaultValue: '0',
                dataInit: asignarAncho,
                value:diasSemana
            }
        },
        {'label': 'Hora Inicio', name: 'hora_inicio', index: 'hora_inicio', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1},edittype:'select',
            editoptions: {
                value:"0700:07:00 AM;0730:07:30 AM;0800:08:00 AM;0830:08:30 AM;0900:09:00 AM;0930:09:30 AM;1000:10:00 AM;1030:10:30 AM;1100:11:00 AM;1130:11:30 AM;1200:12:00 M;1230:12:30 PM;0100:01:00 PM;0130:01:30 PM;0200:02:00 PM;0230:02:30 PM;0300:03:00 PM;0330:03:30 PM;0040:04:00 PM;0430:04:30 PM;0500:05:00 PM;0530:05:30 PM;0600:06:00 PM;0630:06:30 PM;0700:07:00 PM;0730:07:30 PM;0800:08:00 PM"
            }
        },
        {'label': 'Hora Fin', name: 'hora_fin', index: 'hora_fin', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1},edittype:'select',
            editoptions: {
                value:"0700:07:00 AM;0730:07:30 AM;0800:08:00 AM;0830:08:30 AM;0900:09:00 AM;0930:09:30 AM;1000:10:00 AM;1030:10:30 AM;1100:11:00 AM;1130:11:30 AM;1200:12:00 M;1230:12:30 PM;0100:01:00 PM;0130:01:30 PM;0200:02:00 PM;0230:02:30 PM;0300:03:00 PM;0330:03:30 PM;0040:04:00 PM;0430:04:30 PM;0500:05:00 PM;0530:05:30 PM;0600:06:00 PM;0630:06:30 PM;0700:07:00 PM;0730:07:30 PM;0800:08:00 PM"
            }
        },
        {'label': 'Id Monitor', name: 'id_monitor', index: 'id_monitor', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1},edittype:'select',
            editoptions: {
                /*dataUrl: 'controlador/fachada.php?clase=horas_disponibles_monitor&oper=getSelect',
                dataInit: asignarAncho,
                defaultValue: '0'*/
                dataUrl: 'controlador/fachada.php?clase=monitor&oper=getMonitorid',
                dataInit: asignarAncho
                
            }
        },
        {'label': 'Nombre Monitor', name: 'nombre', index: 'nombre', width: 100, sortable: true,  editrules: { number: false       , minValue: 1},
            editoptions: {
                /*dataUrl: 'controlador/fachada.php?clase=nombre&oper=getSelect',
                dataInit: asignarAncho,
                defaultValue: '0'*/
                dataInit: asignarAncho,
              
                
            }
        
        }

        
    ];

    function valoresSelect1(){

        
        valores = "lunes:lunes;martes:martes;miercoles:miercoles;jueves:jueves;viernes:viernes;sabado:sabado";
        return valores;
    }

    function valoresSelect2(){
        //modificar para coger valores del id monitor de la BD
        
        valores = "7:7 AM;8:8 AM;9:9 AM;10:10 AM;11:11 AM;12:12 M;13:1 PM;14:2 PM;15:3 PM;16:4 PM;17:5 PM;18:6 PM;19:7 PM;20:8 PM";
        return valores;
    }


    


    function valoresSelect3(){
        
        
        valoresID="";      
        $.ajax({
            type: 'POST',
            url: "controlador/fachada.php?clase=monitor&oper=selectIds",
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

    

    // inicializa el grid
    var grid = jQuery('#horas_disponibles_monitor-grid').jqGrid({
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
        sortname: 'id_horario', // <-- OJO pueden ir varias columnas separadas por comas
        sortorder: "asc",
        height: altoGrid,
        width: anchoGrid,
        pager: "#" + idPager,
        viewrecords: true,
        caption: "Horas Disponibles Monitor",
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
        beforeSubmit : function(postdata, formid) { 
            if(moment(postdata.hora_fin).isAfter(postdata.hora_inicio)){
                return[true,"Success"]; 
            }else{
                return[false,"Fecha inicio debe ser menor a fecha fin."];
            }
        },
        afterSubmit: respuestaServidor
    }, {// add
        width: 420,
        modal: true,
        beforeSubmit : function(postdata, formid) { 
            if(moment(postdata.hora_fin).isAfter(postdata.hora_inicio)){
                return[true,"Success"]; 
            }else{
                return[false,"Fecha inicio debe ser menor a fecha fin."];
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


// JavaScript Document