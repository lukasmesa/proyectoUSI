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

    var clase = 'grupo';  // la clase que implementa el CRUD para este grid
    var idPager = 'grupo-pager';  // la barra de navegación del grid ubicada en la parte inferior

    // las columnas de un grid se definen como un array de objetos con múltiples atributos
    var columnas = [
        {'label': 'N&uacute;mero Grupo', name: 'numero_grupo', index: 'numero_grupo', width: 100, sortable: true, editable: true,editrules: {required: true, number: false, minValue: 1},
            editoptions: {dataInit: asignarAncho}
        },
        {'label': 'Id Docente', name: 'id_docente', index: 'id_docente', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1},edittype:'select',
            editoptions: {
                dataInit: asignarAncho,
                dataUrl: 'controlador/fachada.php?clase=docente&oper=getSelectDocente'
            }
        },
        {'label': 'C&oacute;digo Asignatura', name: 'codigo_asignatura', index: 'codigo_asignatura', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1},edittype:'select',
            editoptions: {
                dataInit: asignarAncho,
                value:valoresSelect2()
            }
        },        
        {'label': 'Color', name: 'color', index: 'color', width: 100, sortable: true, editable: true,hidden:true, editrules: {required: true, number: false, minValue: 1,edithidden:true},            
            editoptions: {
                dataInit: function (e) {
                    $(e).attr("type", "color");
                 }
            }
        }
    ];
	
	function valoresSelect1(){
        valoresIdDocente="";      
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
                    valoresIdDocente+=s;
                }            
            },
            async:false
        });
        return valoresIdDocente.substr(0,(valoresIdDocente.length-1));    
    }

        function valoresSelect2(){
        //hacer corresponder con el nombre de la asignatura
        valoresCodAsignatura="";      
        $.ajax({
            type: 'POST',
            url: "controlador/fachada.php?clase=asignatura&oper=selectCodAsignaturas",
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
                    valoresCodAsignatura+=s;
                }            
            },              
            async:false
        });
        return valoresCodAsignatura.substr(0,(valoresCodAsignatura.length-1));    
    }

    // inicializa el grid
    var grid = jQuery('#grupo-grid').jqGrid({
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
        sortname: 'codigo_grupo', // <-- OJO pueden ir varias columnas separadas por comas
        sortorder: "asc",
        height: altoGrid,
        width: anchoGrid,
        pager: "#" + idPager,
        viewrecords: true,
        caption: "Grupo",
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
		beforeSubmit: function (postdata) {   //  OJO  <<<< 
            postdata.color = $('#color').val();
        },
        afterSubmit: respuestaServidor
    }, {// add
        width: 420,
        modal: true,
		beforeSubmit: function (postdata) {   //  OJO  <<<< 
            postdata.color = $('#color').val();
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
        if (columna == 'codigo_grupo') {
            if (valor === '0') {
                return [false, "Falta seleccionar el codigo del grupo"];
            }
        }
        if (columna == 'id_docente') {
            if (valor === '0') {
                return [false, "Falta seleccionar codigo de asignatura"];
            }
        }
        return [true, ""];
    }
});