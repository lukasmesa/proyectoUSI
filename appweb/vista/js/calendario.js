/**
 * Created by Jhonatan Ballesteros on 13/11/2016.
 */

$(document).ready(function() {
    var calendario;
    var filtro="Sala";
    var anchoEtiquetas = 100;
    var anchoContenedor = 500;
    var lista_Actividades = ["Selecciones una actividad","Monitoria", "Clase", "Evento"];
    var filtros=["Seleccione un Filtro","Docente","Sala","Monitor"];
    jQuery('#calendario-start').datetimepicker({
        step: 30, // listado de horas con cambio cada media hora
        format: 'Y-m-d H:i'
    });
    jQuery('#calendario-end').datetimepicker(

        {
            step: 30, // listado de horas con cambio cada media hora
            format: 'Y-m-d H:i'
        }
    );

    for (var i = 0; i < lista_Actividades.length; i++) {
        var x = document.getElementById("calendario-actividad");
        var option = document.createElement("option");
        option.text = lista_Actividades[i];
        option.value = i;
        x.appendChild(option);
    }
    $('#calendario-filtros').html('');
    for (var j = 0; j < filtros.length; j++) {
        $('#calendario-filtros').append($('<option>', {
            value: filtros[j],
            text: filtros[j]
        }));
    }

    var lista_salas = getElementos({'clase': 'sala', 'oper': 'getSelectSala', 'json': true});
    $('#calendario-sala').html(lista_salas);

    $("#calendario-actividad").on('change', function () {

        if (this.value == 1) {

            var lista_monitores = getElementos({'clase': 'monitor', 'oper': 'getSelectMonitor', 'json': true});
            $('#calendario-usuario').html(lista_monitores);

        }
        else if (this.value ==2 || this.value == 3) {

            var lista_docentes = getElementos({'clase': 'docente', 'oper': 'getSelectDocente', 'json': true});
            $('#calendario-usuario').html(lista_docentes);
        }
    });
    $('#calendario-filtros').on('change',function () {
        if(this.value=='Docente'||this.value=='Monitor'||this.value=='Sala') {

            filtro = this.value;
            calendario.fullCalendar("refetchEvents");
        }
    });
    $("#calendario-dialog").estiloFormulario({
        //'claseFormulario': 'box',
        'anchoFormulario': anchoContenedor + 'px',
        'anchoEtiquetas': anchoEtiquetas + 'px',
        'anchoEntradas': (anchoContenedor - anchoEtiquetas - 40) + 'px',
        'alturaTextArea': '50px'
    });

    // el formulario para agregar y editar
    $('#calendario-dialog').dialog({
        autoOpen: false,
        width: anchoContenedor + 10,
        height: 280,
        modal: true
    });
    // página cargada, inicializamos el calendario...
    calendario = $('#calendario-calendario').fullCalendar({
        theme: true,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay,listMonth'
        },
        defaultView: 'agendaWeek',
        height: 600,
        width: 650,
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
        events: {
            url: 'controlador/fachada.php',
            type: 'POST',
            data: function () {
                return {
                    clase: 'cronograma',
                    oper: 'getProgramacion',
                    caso: filtro
                }
            }, error: function () {
               // mostrarMensaje('Problemas al intentar cargar los turnos', '#turno_produccion-mensaje')
            }

        },
        eventResize: function (event, delta, revertFunc) {

            redimensionarActividad(event);
        },
        eventDrop: function (event, delta, revertFunc) {
            moverActividad(event);
        },
        eventRender: function (event, element) {
            // Desplegar información complementaria del turno
            element.qtip({
                content: {
                    text: event.descripcion+"\nusuario:"+event.nombre
                }
            });
        }
    });

    function mostrarInformacion(evento){

    }
    function nuevaActividad(start, end) {
        console.log('agregando turnos');

        // se transfiere a los campos del formulario el rango seleccionado
        $("#calendario-start").val(start.format("YYYY-MM-DD HH:mm"));
        $("#calendario-end").val(end.format("YYYY-MM-DD HH:mm"));


        var formulario = $("#calendario-dialog").dialog("option", "buttons", [
            {
                id: "btnGuardar", text: "Guardar", click: function () {
                //aca va el metodo para agregar una actividad
                    agregarActividad(formulario);

            }
            },
            {
                id: "btnCancelar", text: "Cancelar", icons: {primary: "ui-icon-close"}, click: function () {
                $(this).dialog("close");
            }
            }
        ]).dialog("open");
    }

    function actualizarActividad(event) {
        // se transfiere a los campos del formulario el evento seleccionado

        $("#calendario-start").val(event.start.format("YYYY-MM-DD HH:mm"));
        $("#calendario-end").val(event.end.format("YYYY-MM-DD HH:mm"));

        var formulario = $("#calendario-dialog").dialog("option", "buttons", [
            {   id: "btnGuardar", text: "Guardar", click: function () {
                //aca va el metodo para agregar una actividad
                    agregarActividad(formulario);
            }
            },
            {
                id: "btnActualizar", text: "Actualizar", click: function () {
                //aca va el metodo para editar la actividad
                editarActividad(formulario,event);
            }
            },
            {
                id: "btnEliminar", text: "Eliminar", click: function () {
                //aca va el metodo para eliminar una actividad
                    eliminarActividad(formulario,event);
            }
            },
            {
                id: "btnCancelar", text: "Cancelar", icons: {primary: "ui-icon-close"}, click: function () {
                $(this).dialog("close");
            }
            }            
            
        ]).dialog("open");
    }


    function agregarActividad(formulario) {
        var idUsuario = $("#calendario-usuario").val();

        var start = $("#calendario-start").val();

        var end = $("#calendario-end").val();

        var sala = $("#calendario-sala").val();
        var fecha_reserva=calendario.fullCalendar('getDate').format('YYYY-MM-DD H:mm'); //la hroa y fecha del regustro de la actividad

        var tipo=$("#calendario-actividad").val();
        var descripcion=$("#calendario-descripcion").val();
        var estado_reserva=0;
        //alert(idUsuario+"\n"+start+"\n"+end+"\n"+sala+"\n"+fecha_reserva+"\n"+estado_reserva+"\n"+tipo+"\n"+descripcion+"\n");
        // si start y end de los campos tiene dato, reemplazar lo que llega como argumentos
        // si end es vacío, entonces start + 1 hora

        if (idUsuario !== '0' && start && end) {

            var datosCronograma = {
                id_usuario: idUsuario,
                fecha_ini_prestamo: start,
                fecha_fin_prestamo: end,
                id_sala: sala,
                fecha_reserva:fecha_reserva,
                descripcion:descripcion,
                tipo:tipo,
                estado_reserva:estado_reserva
                // y así sucesivamente para otros campos que hagan falta
                //title: $("#turno_produccion-maquina option:selected").text(),
                //color: $("#turno_produccion-maquina option:selected").attr('color')
            };
            var datosConcatenados={
              title:descripcion,
               start:start,
                end:end
            };
            $.post("controlador/fachada.php", {
                clase: 'cronograma',
                oper: 'agregarActividad',
                turno: datosCronograma
            }, function (data) {
                if (data.ok) {
                    //alert("entre a insertar");
                    // argumento 3 > stick = true => persistirá en el calendario (no quiero eso)

                    calendario.fullCalendar('renderEvent', datosConcatenados, false);
                    calendario.fullCalendar('unselect');
                    formulario.dialog("close");
                } else {
                    console.log(data);
                }
            }, "json").always(function () {
                // $.unblockUI();
            });
        } else {
            console.log('falló la inserción. Datos incompletos o erróneos');
        }

    }


    function editarActividad(formulario, evento) {
        var idUsuario = $("#calendario-usuario").val();
        var start = $("#calendario-start").val();
        var end = $("#calendario-end").val();

        // si start y end de los campos tiene dato, reemplazar lo que llega como argumentos
        // si end es vacío, entonces start + 1 hora
        if (idUsuario !== '0' && start && end) {
            evento.id_usuario = idUsuario;
            evento.start = start;
            evento.end = end;
            evento.fecha_ini_prestamo=start;
            evento.fecha_fin_prestamo=end;
            evento.id_sala=$("#calendario-sala").val();
            evento.tipo=$("#calendario-actividad").val();
            evento.descripcion=$("#calendario-descripcion").val();
            // y así sucesivamente para otros campos
            evento.title = $("#calendario-descripcion").val()+" En la"+$("#calendario-sala").val();
            //evento.color = $("#calendario-usuario option:selected").attr('color');

            $.post("controlador/fachada.php", {
                clase: 'cronograma',
                oper: 'actualizarActividad',
                caso: 'actualizar',
                turno: {
                    id_reserva: evento.id,
                    id_usuario: idUsuario,
                    start: start,
                    end: end,
                    sala:evento.id_sala,
                    tipo:evento.tipo,
                    descripcion:evento.descripcion
                    }
            }, function (data) {

                if (data.ok) {
                    calendario.fullCalendar('updateEvent', evento);
                    calendario.fullCalendar('unselect');
                    formulario.dialog("close");
                } else {
                    console.log(data);
                }
            }, "json").always(function () {
                // $.unblockUI();
            });
        } else {
            console.log('falló la actualización. Datos incompletos o erróneos');
        }
        console.log(evento);
    }



    function eliminarActividad(formulario, evento) {
        if (confirm('Confirme por favor si esta seguro de lo que trata de hacer')) {
            $.post("controlador/fachada.php", {
                clase: 'cronograma',
                oper: 'eliminarActividad',
                idCronograma: evento.id
            }, function (data) {
                if (data.ok) {
                    calendario.fullCalendar('removeEvents', evento.id)
                } else {
                    console.log(data);
                }
            }, "json").always(function () {
                // $.unblockUI();
            });
            formulario.dialog("close");
        }
    }

    function moverActividad(evento) {
        $.post("controlador/fachada.php", {
            clase: 'cronograma',
            oper: 'actualizarActividad',
            caso: 'mover',
            turno: {
                id_reserva: evento.id,
                start: evento.start.format(),
                end: evento.end.format()
            }
        }, function (data) {
            if (!data.ok) {
                console.log(data);
            }
        }, "json").always(function () {
            // $.unblockUI();
        });
    }

    function redimensionarActividad(evento) {
        $.post("controlador/fachada.php", {
            clase: 'cronograma',
            oper: 'actualizarActividad',
            caso: 'redimensionar',
            turno: {
                id_reserva: evento.id,
                end: evento.end.format()
            }
        }, function (data) {
            if (!data.ok) {
                console.log(data);
            }
        }, "json").always(function () {
            // $.unblockUI();
        });
    }


});