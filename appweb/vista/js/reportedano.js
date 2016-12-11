$(function () {

    $(window).resize(); // forzar un resize para detectar el ancho del contenedor (ver index.js)
    var anchoGrid = anchoContenedor; // se asigna a una variable local el ancho del contenedor
    var altoGrid = $(window).height() - 350;

    if (altoGrid < 200) {
        altoGrid = 200;
    }

    var clase = 'reportedano';  // la clase que implementa el CRUD para este grid
    var idPager = 'reporte_daño-pager';  // la barra de navegación del grid ubicada en la parte inferior

    // las columnas de un grid se definen como un array de objetos con múltiples atributos
    var columnas = [
       /* {'label': 'id_reporte', name: 'id_reporte', index: 'id_reporte', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1},
            editoptions: {
                dataInit: asignarAncho,
                defaultValue:function()
                {
                    return jQuery("#reporte_daño-grid").jqGrid('getGridParam', 'records') +1;
                }
            }
        },*/
        {'label': 'Descripci&oacute;n', name: 'descripcion', index: 'descripcion', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1},
            editoptions: {dataInit: asignarAncho}
        },        
        {'label': 'Usuario', name: 'id_usuario', index: 'id_usuario', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1},edittype:'select',
            editoptions: {dataInit: asignarAncho,
                dataUrl: 'controlador/fachada.php?clase=usuario&oper=getSelectUsuario2'
            }
        },
        {'label': 'Id Equipo Sala' , name: 'id_equipo_sala', index: 'id_equipo_sala', width: 100, sortable: true, editable: true, edittype:'select',
            editoptions: {
                dataInit: asignarAncho,
                dataUrl: 'controlador/fachada.php?clase=equipos_sala&oper=selectIdEquipos'
            }
        }

    ];
	
	function valoresSelect1(){
        valoresIDEquipo="";      
        $.ajax({
            type: 'POST',
            url: "controlador/fachada.php?clase=equipos_sala&oper=selectIdsEquipo",
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

        function valoresSelect2(){
        valoresIDUsuario="";      
        $.ajax({
            type: 'POST',
            url: "controlador/fachada.php?clase=reportedano&oper=selectIDUSuario",
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
                    valoresIDUsuario+=s;                
                }                                
            },              
            async:false
        });
        return valoresIDUsuario.substr(0,(valoresIDUsuario.length-1));  
    }

    // inicializa el grid
    var grid = jQuery('#reporte_daño-grid').jqGrid({
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
        sortname: 'id_reporte', // <-- OJO pueden ir varias columnas separadas por comas
        sortorder: "asc",
        height: altoGrid,
        width: anchoGrid,
        pager: "#" + idPager,
        viewrecords: true,
        caption: "Reporte de daños",
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

        if (columna == 'id_reporte') {
            if (valor === '0') {
                return [false, "Falta seleccionar identificador usuario externo"];
            }
        }
        if (columna == 'Usuario') {
            if (valor === '0') {
                return [false, "Falta seleccionar identificador del usuario"];
            }
        }
        return [true, ""];
    }
});