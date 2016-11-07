/* 
 * Control de opciones para generales de la aplicación
 * Se quiere que las instrucciones que hay dentro de la función anónima function () {..};
 * se ejecuten cuando la aplicación haya sido cargada, por eso se usa on ready:
 * $(document).on('ready', function () {...});
 * los demás script de páginas sólo requerirán la función principal
 */

'use strict';

var initDatePicker = {
    dateFormat: 'yy-mm-dd',
    minDate: new Date(2010, 0, 1),
    maxDate: new Date(2020, 0, 1),
    showOn: 'focus'
};
var estadosProduccion;

var anchoContenedor;

$(document).on('ready', function () {

    // una de las formas de manipular el css mediante jQuery
    $("#index-monitorias, #index-salas, #index-cronograma, #index-reportes,#index-historiales").css({'width': '13em'});

    // manejo de eventos con jQuery


    $("#index-monitorias").button().on("click", function () {

    });
    $("#index-salas").button().on("click", function () {

    });
    $("#index-cronograma").button().on("click", function () {
        cargarPagina("#index-contenido", "vista/html/cronograma.html");
    });
    $("#index-reportes").button().on("click", function () {

    });
    $("#index-historiales").button().on("click", function () {

    });



    // ejemplo de llamado de una instrucción $.post
    $.post("controlador/fachada.php", {
        clase: 'UtilConexion',
        oper: 'getEstadosProduccion'
    }, function (estados) {
        console.log(estados);
        estadosProduccion = estados;
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
    var respuesta = jQuery.parseJSON(response.responseText);
    console.log(respuesta);
    return [respuesta.ok, "El servidor no pudo completar la acción"];
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
