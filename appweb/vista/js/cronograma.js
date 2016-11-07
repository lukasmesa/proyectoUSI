/**
 * Created by Jhonatan on 05/11/2016.
 */

$(document).ready(function() {
    var calendario;
    var anchoEtiquetas = 100;
    var anchoContenedor = 500;

    jQuery('#cronograma-start').datetimepicker({
        step: 30, // listado de horas con cambio cada media hora
        format: 'Y.m.d H:i'
    });
    jQuery('#cronograma-end').datetimepicker();

    


    $("#cronograma-dialog").estiloFormulario({
        //'claseFormulario': 'box',
        'anchoFormulario': anchoContenedor + 'px',
        'anchoEtiquetas': anchoEtiquetas + 'px',
        'anchoEntradas': (anchoContenedor - anchoEtiquetas - 40) + 'px',
        'alturaTextArea': '50px'
    });

    // el formulario para agregar y editar
    $('#cronograma-dialog').dialog({
        autoOpen: false,
        width: anchoContenedor + 10,
        height: 280,
        modal: true
    });
    // página cargada, inicializamos el calendario...
    calendario=$('#cronograma-calendario').fullCalendar({
        theme: true,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay,listMonth'
        },
        height : 600,
        width  : 650,
        selectable: true,
        selectHelper: true,
        editable: true,

        select: function (start, end) {

            nuevaActividad(start, end);
        },
        eventLimit: true,
        eventClick: function (calEvent, jsEvent, view) {

            actualizarActividad(calEvent);
        },
        events : [
            {
                title  : 'event1',
                start  : '2016-11-01 12:30:00',
                end : '2016-11-01 01:30:00'
            },
            {
                title  : 'event2',
                start  : '2016-11-05',
                end    : '2016-11-07'
            },
            {
                title  : 'event3',
                start  : '2016-11-09 12:30:00',
                allDay : false // will make the time show
            }
        ]
    });

    function nuevaActividad(start, end) {
        console.log('agregando turnos');

        // se transfiere a los campos del formulario el rango seleccionado
        $("#cronograma-start").val(start.format("YYYY-MM-DD HH:mm"));
        $("#cronograma-end").val(end.format("YYYY-MM-DD HH:mm"));


        var formulario = $("#cronograma-dialog").dialog("option", "buttons", [
            {
                id: "btnGuardar", text: "Guardar", click: function () {
                    //aca va el metodo para agregar una actividad

            }
            },
            {id: "btnCancelar", text: "Cancelar", icons: {primary: "ui-icon-close"}, click: function () {
                $(this).dialog("close");
            }
            }
        ]).dialog("open");
    }
    function actualizarActividad(event) {
        // se transfiere a los campos del formulario el evento seleccionado

        $("#cronograma-start").val(event.start.format("YYYY-MM-DD HH:mm"));
        $("#cronograma-end").val(event.end.format("YYYY-MM-DD HH:mm"));

        var formulario = $("#cronograma-dialog").dialog("option", "buttons", [
            {
                id: "btnActualizar", text: "Actualizar", click: function () {
                    //aca va el metodo para editar la actividad

            }
            },
            {
                id: "btnEliminar", text: "Eliminar", click: function () {
                    //aca va el metodo para eliminar una actividad

            }
            },
            {id: "btnCancelar", text: "Cancelar", icons: {primary: "ui-icon-close"}, click: function () {
                $(this).dialog("close");
            }
            }
        ]).dialog("open");
    }
});