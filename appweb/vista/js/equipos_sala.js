$(function () {

    $(window).resize(); // forzar un resize para detectar el ancho del contenedor (ver index.js)
    var anchoGrid = anchoContenedor; // se asigna a una variable local el ancho del contenedor
    var altoGrid = $(window).height() - 350;

    if (altoGrid < 200) {
        altoGrid = 200;
    }

    var clase = 'equipos_sala';  // la clase que implementa el CRUD para este grid
    var idPager = 'equipos_sala-pager';  // la barra de navegación del grid ubicada en la parte inferior

    var field1, check_function1 = function (value, colname)
    {

        if (colname === "nombre") {
            field1 = value;
        }

        if (value.length < 3) {
            return [false, "El id del equipo debe tener como minimo 6 caracteres"];
        }

        return [true];
    };
	
    var field1, check_function2 = function (value, colname)
    {

        if (colname === "descripcion") {
            field1 = value;
        }

        if (value.length >= 50) {
            return [false, "Se han excedido la cantidad de caracteres de la descripcion"];
        }
        return [true];
    };
	
	var field1, check_function3 = function (value, colname)
    {

        if (colname === "software_equipo") {
            field1 = value;
        }

        if (value.length >= 50) {
            return [false, "Se han excedido la cantidad de caracteres de la descripcion del Software del equipo"];
        }
        return [true];
    };

    var field1, check_function4 = function (value, colname)
    {

        if (colname === "partes_equipo") {
            field1 = value;
        }

        if (value.length >= 800) {
            return [false, "Se han excedido la cantidad de caracteres de la descripcion de las partes equipo"];
        }
        return [true];
    };
    // las columnas de un grid se definen como un array de objetos con múltiples atributos
    var columnas = [
        {'label': 'Id Equipo Sala', name: 'id_equipo_sala', index: 'id_equipo_sala', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1, custom: true, custom_func: check_function1},
            editoptions: {dataInit: asignarAncho}
        },
        {'label': 'Descripci&oacute;n', name: 'descripcion', index: 'descripcion', width: 100, sortable: true, editable: true, editrules: {required: false, number: false, minValue: 1, custom: true, custom_func: check_function2},
            editoptions: {dataInit: asignarAncho}
        },
        {'label': 'Estado', name: 'estado', index: 'estado', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1},edittype:'select',
            editoptions: {defaultValue: '0',
                dataInit: asignarAncho,
                value:estadosEquipos
            }
        },        
        {'label': 'Software Equipo', name: 'software_equipo', index: 'id_parte', width: 100, sortable: true, editable: true,
            editoptions: {dataInit: asignarAncho}
        },
        {'label': 'Partes Equipo', name: 'partes_equipo', index: 'id_sofware', width: 100, sortable: true, editable: true,
            editoptions: {dataInit: asignarAncho}
        },
        {'label': 'Id Sala', name: 'id_sala', index: 'id_sala', width: 100, sortable: true, editable: true, editrules: {required: true, number: false, minValue: 1},edittype:'select',
            editoptions: {defaultValue: '0',
                dataUrl: 'controlador/fachada.php?clase=sala&oper=getSelectSala',
                dataInit: asignarAncho
            }
        },
        {'label': 'Nombre Sala', name: 'nombre_sala', index: 'nombre_sala', width: 100, sortable: true, 
            editoptions: {dataInit: asignarAncho}
        }        
    ];

    function valoresSelect1(){

        valoresIdsS="";      
        $.ajax({
            type: 'POST',
            url: "controlador/fachada.php?clase=sala&oper=selectIdsSala",
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
                    valoresIdsS+=s;                
                }                                
            },              
            async:false
        });
        return valoresIdsS.substr(0,(valoresIdsS.length-1)); 
    }

    function valoresSelect2(){

        valores = "correcto:correcto;dañado:dañado;reparacion:reparacion";
        return valores;
    }
    // inicializa el grid
    var grid = jQuery('#equipos_sala-grid').jqGrid({
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
        sortname: 'id_equipo_sala', // <-- OJO pueden ir varias columnas separadas por comas
        sortorder: "asc",
        height: altoGrid,
        width: anchoGrid,
        pager: "#" + idPager,
        viewrecords: true,
        caption: "Equipos de sala",
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


     //------------------------------------------------------------------------
     //validaciones campo 1
    function validarOrdenProduccion(valor, columna) {

        if (columna == 'id_equipo_sala') {
            if (valor === '0') {
                return [false, "Falta seleccionar la el id del equipo"];
            }
        }
        if (columna == 'descripcion') {
            if (valor === '0') {
                return [false, "Falta seleccionar la descripcion del equipo"];
            }
        }
        if (columna == 'estado') {
            if (valor === '0') {
                return [false, "Falta seleccionar la estado del equipo"];
            }
        }
        if (columna == 'nombre_sala') {
            if (valor === '0') {
                return [false, "Falta seleccionar el nombre de la sala del equipo"];
            }
        }
        return [true, ""];
    }


    //validacion campo 2

var field1,check_function1 = function(value,colname) 
    {

        if (colname === "id_equipo_sala") {
            field1 = value;
        } 
        
        if(value.length<3){
            return [false, "El codigo del equipo debe tener una longitud mayor a 2"];
        }
        return [true];
    };

    var field1,check_function2 = function(value,colname) 
    {
        
        if (colname === "descripcion") {
            field1 = value;
        }       
          
        if(value.length>=50){
            return [false, "Se han exedido la cantidad de caracteres de la descripcion"];
        }       
        return [true];
    };

    var field1,check_function3 = function(value,colname) 
    {
        
        if (colname === "estado") {
            field1 = value;
        }       
          
        if(value!='dañado'&& value!= 'correcto'&& value!='reparacion'){
            console.log("t",value,colname);
            return [false, "igrese uno de los valores correcto , dañado o reparacion"];
        }
               
        return [true];
    };

    var field1,check_function4 = function(value,colname) 
    {
        
        if (colname === "nombre_sala") {
            field1 = value;
        }       
          
        if(value.length<2){
           
            return [false, "El el nombre de la sala debe tener una longitud mayor a 1"];
        }
        
        return [true];
    };

    var field1,check_function5 = function(value,colname) 
    {
        
        if (colname === "id_parte") {
            field1 = value;
        }       
          
        if(value.length<1){
            return [false, "El id de la parte debe ser utener una longitud mayor a 0"];
        }
        if( isNaN(valor) ) {
             return [false, "El id de la parte debe ser numerico"];
        } 
        
        return [true];
    };


    var field1,check_function6 = function(value,colname) 
    {
        
        if (colname === "id_sofware") {
            field1 = value;
        }       
          
        if(value.length<1){
            return [false, "El id del software debe tener una longitud mayor a 0"];
        }
        if( isNaN(valor) ) {
             return [false, "El id del software debe ser numerico"];
        } 
        
        return [true];
    };



});


