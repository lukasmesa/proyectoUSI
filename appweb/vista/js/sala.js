$(function () {

    $(window).resize(); // forzar un resize para detectar el ancho del contenedor (ver index.js)
    var anchoGrid = anchoContenedor; // se asigna a una variable local el ancho del contenedor
    var altoGrid = $(window).height() - 350;

    if (altoGrid < 200) {
        altoGrid = 200;
    }

    var clase = 'sala';  // la clase que implementa el CRUD para este grid
    var idPager = 'sala-pager';  // la barra de navegación del grid ubicada en la parte inferior

    var field1,check_function1 = function(value,colname) 
    {
        
        if (colname === "nombre_sala") {
            field1 = value;
        } 
        
        if(value.length<1){
            console.log("t",value,colname);
            return [false, "El nombre debe tener logitud mayor a 1  "];
        }
        else
        {
            return [true];
        }
        
        return [true];
    };

var field1,check_function2 = function(value,colname) 
    {
        
        if (colname === "capacidad") {
            field1 = value;
        } 
        
        if(value.length<0){
            console.log("t",value,colname);
            return [false, "Capacidad de la sala incorrecta "];
        }
        else
        {
            return [true];
        }
        
        return [true];
    };

var field1,check_function3 = function(value,colname) 
    {
        
        if (colname === "descripcion") {
            field1 = value;
        } 
        
        if(value.length>50){
            console.log("t",value,colname);
            return [false, "La descripcion debe ser mas corta"];
        }
        else
        {
            return [true];
        }
        
        return [true];
    };
    // las columnas de un grid se definen como un array de objetos con múltiples atributos
    var columnas = [
        /*{'label': 'Id Sala', name: 'id_sala', index: 'id_sala', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1,custom:true,custom_func:check_function1},
            editoptions: {dataInit: asignarAncho}
        },*/
        {'label': 'Nombre Sala', name: 'nombre_sala', index: 'nombre_sala', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1,custom:true,custom_func:check_function1},
            editoptions: {dataInit: asignarAncho}
        },
        {'label': 'Capacidad', name: 'capacidad', index: 'capacidad', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1,custom:true,custom_func:check_function2},
            editoptions: {dataInit: asignarAncho}
        },
        {'label': 'Descripci&oacute;n', name: 'descripcion', index: 'descripcion', width: 100, sortable: true, editable: true, editrules: {required: false, number: false, minValue: 1,custom:true,custom_func:check_function3},
            editoptions: {dataInit: asignarAncho}
        },
        {'label': 'Nombre Bloque', name: 'nombre_bloque', index: 'nombre_bloque', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1},edittype:'select',
            editoptions: {
                dataInit: asignarAncho,
                value:valoresSelect()}
        },
        {'label': 'Color', name: 'color', index: 'color', width: 100, sortable: true, editable: true,hidden:true, editrules: {required: true, number: false, minValue: 1,edithidden:true},
            
            editoptions: {
                dataInit: function (e) {
                    $(e).attr("type", "color");
                 }
            }
        }
    ];
	
	function valoresSelect(){
        valoresNombreBloque="";      
        $.ajax({
            type: 'POST',
            url: "controlador/fachada.php?clase=bloque&oper=selectNombresBloque",
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
                    valoresNombreBloque+=s;                
                }                                
            },              
            async:false
        });
        return valoresNombreBloque.substr(0,(valoresNombreBloque.length-1)); 
    }

    // inicializa el grid
    var grid = jQuery('#sala-grid').jqGrid({
        url: 'controlador/fachada.php',
        datatype: "json",
        mtype: 'POST',
        postData: {
            clase: clase,
            oper: 'select'
        },
        rowNum: 10,
        rowList: [10,20, 30],
        colModel: columnas,
        autowidth: false,
        shrinkToFit: false,
        sortname: 'nombre_sala', // <-- OJO pueden ir varias columnas separadas por comas
        sortorder: "asc",
        height: altoGrid,
        width: anchoGrid,
        pager: "#" + idPager,
        viewrecords: true,
        caption: "Sala",
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

        if (columna == 'nombre_sala') {
            if (valor === '0') {
                return [false, "Falta seleccionar la el nombre de la sala"];
            }
        }
        if (columna == 'capacidad') {
            if (valor === '0') {
                return [false, "Falta seleccionar la capacidad de la sala"];
            }
        }
        if (columna == 'descripcion') {
            if (valor === '0') {
                return [false, "Falta seleccionar la descripcion de la sala"];
            }
        }
        if (columna == 'nombre_bloque') {
            if (valor === '0') {
                return [false, "Falta seleccionar el nombre del bloque"];
            }
        }
        return [true, ""];
    }

});