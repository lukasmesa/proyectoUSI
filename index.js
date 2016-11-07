/* 
 * Control de opciones para generales de la aplicación
 * Se quiere que las instrucciones que hay dentro de la función anónima function () {..};
 * se ejecuten cuando la aplicación haya sido cargada, por eso se usa on ready:
 * $(document).on('ready', function () {...});
 * los demás script de páginas sólo requerirán la función principal
 */

'use strict';

var anchoContenedor;

$(document).on('ready', function () {

    // una de las formas de manipular el css mediante jQuery
    var opciones = "#index-cronograma";
    $(opciones).css({'width': '13em'});
    
    $("#index-cronograma").button().on("click", function () {
        cargarPagina("#index-contenido", "vista/html/cronograma.html");
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
    $.post("controlador/fachada.php", {
        clase: 'UtilConexion',
        oper: 'getEstados'
    }, function (estados) {
        console.log(estados);
    }, 'json');

    // cada que se redimensione el navegador se actualiza anchoContenedor
    $(window).on('resize', function () {
        anchoContenedor = $(window).width() - 220;
        console.log('ancho usable: ' + anchoContenedor)
        $('.ui-jqgrid-btable').each(function () {
            $(this).jqGrid('setGridWidth', anchoContenedor);
        });
    });

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
