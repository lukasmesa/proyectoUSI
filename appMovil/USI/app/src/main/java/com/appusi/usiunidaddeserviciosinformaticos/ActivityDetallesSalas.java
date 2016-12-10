package com.appusi.usiunidaddeserviciosinformaticos;

import android.graphics.Color;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;
import android.widget.TextView;

import com.appusi.usiunidaddeserviciosinformaticos.Clases.Sala;

public class ActivityDetallesSalas extends AppCompatActivity {


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_detalles_salas);
        setTitle("SALAS");
        Sala temp = (Sala)getIntent().getExtras().getSerializable("sala"); //debimos serializar la clase sala

        TextView txtBloque = (TextView) findViewById(R.id.nombre_bloque_info);
        TextView txtNombreSala = (TextView) findViewById(R.id.nombre_sala_info);
        TextView txtCapacidad = (TextView) findViewById(R.id.capacidad_info);
        TextView txtDescripcion = (TextView) findViewById(R.id.descripcion_info);
        View viewColor = (View) findViewById(R.id.color_info);
        TextView txtFecha = (TextView) findViewById(R.id.txt_fecha_inicio_info) ;

        txtBloque.setText("Bloque : "+temp.getNombreBloque());
        txtNombreSala.setText("Nombre : "+temp.getNombre());
        txtCapacidad.setText("Capacidad : "+temp.getCapacidad());
        txtDescripcion.setText("Descripcion : "+temp.getDescripcionPrestamo());
        txtFecha.setText("Fecha : " + temp.getFecha());
        viewColor.setBackgroundColor(Color.parseColor(temp.getColor())); // cambiamos el color


    }
}
