/* 
 * Control de opciones para generales de la aplicación
 * Se quiere que las instrucciones que hay dentro de la función anónima function () {..};
 * se ejecuten cuando la aplicación haya sido cargada, por eso se usa on ready:
 * $(document).on('ready', function () {...});
 * los demás script de páginas sólo requerirán la función principal
 */

'use strict';

var initDatePicker = {
    dateFormat: 'yy-mm-dd hh:ii',
    minDate: new Date(2010, 0, 1),
    maxDate: new Date(2020, 0, 1),
    showOn: 'focus'
};

var anchoContenedor;
var tipoDoc;
var diasSemana;
var estadosEquipos;
var tipoReserva;

$(document).on('ready', function () {

    // una de las formas de manipular el css mediante jQuery    
    var opciones = "#index-CRUDS";
    $(opciones).css({'width': '13em'});   
    $("#index-calendario").css({'width': '13em'});

   
    $("#index-CRUDS").button().on("click", function () {
        cargarPagina("#index-contenido", "vista/html/CRUDS.html");

    });
    $("#index-calendario").button().on("click", function () {
        cargarPagina("#index-contenido", "vista/html/calendario.html");        
    });
    $("#cerrar-sesion").button().on("click", function () {
        location.reload(true);        
    });
    // un ejemplo de uso de selectores jQuery para controlar eventos sobre links
    $("#index-menu-superior li a").each(function () {
        var opcion = $(this).text();

        $(this).on('click', function (event) {
            switch (opcion) {
                case "Actualidad":
                    window.open('http://www.lapatria.com/actualidad');
                    break;
                default:
                    alert('La opción <' + opcion + '> no está disponible');
            }
            event.preventDefault();
        })
    })  // fin de $("#index-menu-superior li a").each(function () {...})

    // otro ejemplo de uso de selectores jQuery para controlar eventos sobre links
    $("#index-pie_pagina a").each(function () {
        var opcion = $(this).text();

        $(this).on('click', function (event) {
            switch (opcion) {
                default:
                    alert('La opción <' + opcion + '> no está disponible');
            }
            event.preventDefault();
        });
    });


    // ejemplo de llamado de una instrucción $.post
 /*   $.post("controlador/fachada.php", {
        clase: 'UtilConexion',
        oper: 'getEstados'
    }, function (estados) {
       // console.log(estados);
    }, 'json');*/
	
	$.post("controlador/fachada.php", {
        clase: 'UtilConexion',
        oper: 'getTipoReserva'
    }, function (tipos) {
        console.log(tipos);
        tipoReserva = tipos;
    }, 'json');

        // ejemplo de llamado de una instrucción $.post
    $.post("controlador/fachada.php", {
        clase: 'UtilConexion',
        oper: 'getTipoDocumento'
    }, function (data) {
        console.log(data);
        tipoDoc = data;
    }, 'json');

        $.post("controlador/fachada.php", {
        clase: 'UtilConexion',
        oper: 'getDiasSemana'
    }, function (data) {
        console.log(data);
        diasSemana = data;
    }, 'json');

        $.post("controlador/fachada.php", {
        clase: 'UtilConexion',
        oper: 'getEstadosEquipos'
    }, function (data) {
        console.log(data);
        estadosEquipos = data;
    }, 'json');

    // cada que se redimensione el navegador se actualiza anchoContenedor
    $(window).on('resize', function () {
        anchoContenedor = $(window).width() - 220;
        console.log('ancho usable: ' + anchoContenedor);
        $('.ui-jqgrid-btable').each(function () {
            $(this).jqGrid('setGridWidth', anchoContenedor);
        });
    });


//$('a.login-window').click(function() {
        
        // Getting the variable's value from a link 
        //var loginBox = $(this).attr('href');
        //alert(loginBox);
        //Fade in the Popup and add close button
        $("#login-box").fadeIn(300);
        
        //Set the center alignment padding + border
        var popMargTop = ($("#login-box").height() + 24) / 2; 
        var popMargLeft = ($("#login-box").width() + 24) / 2; 
        
        $("#login-box").css({ 
            'margin-top' : -popMargTop,
            'margin-left' : -popMargLeft
        });
        
        // Add the mask to body
        $('body').append('<div id="mask"></div>');
        $('#mask').fadeIn(300);
        
        //return false;
    //});
    
    // When clicking on the button close or the mask layer the popup closed
    /*$('a.close, #mask, #entrar').on('click', function() { 
          $('#mask , .login-popup').fadeOut(300 , function() {
            $('#mask').remove();  
        }); 
        return false;
    });*/

    

});

/**
 * Carga el contenido de una página sobre un elemento del DOM
 * @param {type} contenedor el elemento sobre el que se mostrará la página html
 * @param {type} url la dirección de la página html que será mostrada
 */
function cargarPagina(contenedor, url) {
    $(contenedor).load(url, function (response, status, xhr) {
        if (status === "error") {
            alert("Lo siento. Error " + xhr.status + ": " + xhr.statusText);
        }
    });
}

/**
 * Esta función se requiere a nivel global para procesar la respuesta que recibe un objeto jqGrid desde el servidor
 * @param {type} response Una cadena JSON con el estado y el mensaje que envía el servidor luego de procesar una acción
 * @param {type} postdata Los datos que envía jqGrid al servidor
 * @returns {Array} La respuesta del estado de la operación para mostrarla como error si fuese necesario
 */
function respuestaServidor(response, postdata) {
    console.log(postdata)
    var respuesta = jQuery.parseJSON(response.responseText);
    console.log(respuesta);
    return [respuesta.ok, "El servidor no pudo completar la acción"];
}


/**
 * Muestra un mensaje por unos segundos...
 * @param {type} mensaje El texto del mensaje
 * @param {type} elemento El DIV que contendrá el mesaje
 * @returns {undefined}
 */
function mostrarMensaje(mensaje, elemento) {
    mensaje = mensaje + '<br>';
    $(elemento).html(mensaje).show().effect("highlight", {color: '#FA5858'}, 4000).promise().then(function () {
        $(this).hide();
    });
}

/**
 * Devuelve un mensaje formateado para bloquear el sistema mediante BlockUI
 * @param {type} mensaje El texto que se va a formatear
 * @returns {String} el HTML de la cadena formateada
 */
function getMensaje(mensaje) {
    return '<h4><img src="vista/imagenes/ajax-loader.gif"><br>' + mensaje + '<br>Por favor espere...</h4>';
}

function getElementos(parametros) {
    var asincrono, aviso, elementos = new Object(), tipoDatos, url;
    aviso = ("aviso" in parametros) ? parametros['aviso'] : false;
    asincrono = ("async" in parametros) ? parametros['async'] : false;
    tipoDatos = ("tipoDatos" in parametros) ? parametros['tipoDatos'] : "json";
    url = ("url" in parametros) ? parametros['url'] : "controlador/fachada.php";

    $.ajax({
        type: "POST",
        url: url,
        beforeSend: function (xhr) {
            if (aviso) {
                // $.blockUI({message: getMensaje(aviso)});
            }
        },
        data: parametros,
        async: asincrono,
        dataType: tipoDatos
    }).done(function (data) {
        elementos = data;
    }).fail(function () {
       // console.log("Error de carga de datos: " + JSON.stringify(parametros));
        alert("Error de carga de datos");
    }).always(function () {
        if (aviso) {
            // $.unblockUI();
        }
    });
    return elementos;
}

jQuery.fn.estiloFormulario = function (valoresEstilos) {
    var div = this;
    jQuery(this).each(function () {
        var idDiv = $(this).attr('id');
        var item;
        if ($('#' + idDiv + '> ol').length) {
            item = '#' + idDiv + '>ol>li>';
        } else {
            item = '#' + idDiv + '>';
        }

        var estilo = {
            'claseFormulario': '',
            'anchoFormulario': '700px',
            'anchoEtiquetas': '100px',
            'anchoEntradas': '550px',
            'alturaTextArea': '90px',
            'tamanioFuente': '100%',
            'fondo': "url('vista/imagenes/fondo1.jpg') repeat"
        };
        if (typeof (valoresEstilos) === 'object') {
            if (estilo.anchoFormurio === valoresEstilos && estilo.anchoFormulario !== '700px') {
                valoresEstilos = 0
            }
            estilo = $.extend(true, estilo, valoresEstilos);
        }

        $('#' + idDiv).addClass(estilo.claseFormulario);
        $(this).css({
            "font-size": estilo.tamanioFuente,
            "font-family": "Helvetica, sans-serif",
            "width": estilo.anchoFormulario,
            "margin-top": "5px",
            "background": estilo.fondo
        });
        $(item + 'input,' + item + 'textarea,' + item + 'select').css({
            'padding': '5px',
            'width': estilo.anchoEntradas,
            'font-family': 'Helvetica, sans-serif',
            'font-size': estilo.tamanioFuente,
            'margin': '0px 0px 2px 0px',
            'border': '1px solid #ccc'
        });
        $('#' + idDiv + ' :button').css({
            'width': (parseInt(estilo.anchoEntradas) + 11) + 'px'
        });
        // es raro...el ancho de los select no guarda la misma proporción de los otros componentes y hay que hacer ajustes
        $(item + 'select').each(function () {
            if (typeof this.attributes['multiple'] === 'undefined') {
                $(this).css({'width': (parseInt(estilo.anchoEntradas)) + 'px', 'display': 'block'});
            } else {
                // suponiendo un select formateado con el plugin multiselect de Eric Hynds
                $(this).css('width', (parseInt(estilo.anchoEntradas) + 6) + 'px');
            }
        });
        $(item + 'textarea').css("height", estilo.alturaTextArea);
        $(item + 'input,' + item + 'textarea,' + item + 'select').on('focus', function () {
            $(this).css("border", "1px solid #900");
        });
        $(item + 'input,' + item + 'textarea,' + item + 'select').on('blur', function () {
            $(this).css("border", "1px solid #ccc");
        });
        $(item + 'label').css({
            'float': 'left',
            'text-align': 'right',
            'margin-right': '15px',
            'width': estilo.anchoEtiquetas,
            'padding-top': '5px',
            'font-size': estilo.tamanioFuente
        });
        ////  excluir este tipo en los estilos anteriores por ahora dejarlo así/////////////////////
        $(item + 'input:checkbox').css({
            'margin-top': '10px',
            'width': 10
        });
        $(item + 'input,' + item + 'label,' + item + 'button,' + item + 'textarea').css('display', 'block');
    });
    return div;
};