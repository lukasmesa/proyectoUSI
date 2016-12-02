package com.appusi.usiunidaddeserviciosinformaticos;

import android.graphics.Color;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;
import android.widget.TextView;

import com.appusi.usiunidaddeserviciosinformaticos.Clases.Sala;

public class ActivityDetallesSalas extends AppCompatActivity {

    private TextView txtBloque;
    private TextView txtNombreSala;
    private TextView txtCapacidad;
    private TextView txtDescripcion;
    private View viewColor;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_detalles_salas);
        Sala temp = (Sala)getIntent().getExtras().getSerializable("sala"); //debimos serializar la clase sala

        txtBloque = (TextView) findViewById(R.id.nombre_bloque_info);
        txtNombreSala = (TextView) findViewById(R.id.nombre_sala_info);
        txtCapacidad = (TextView) findViewById(R.id.capacidad_info);
        txtDescripcion = (TextView) findViewById(R.id.descripcion_info);
        viewColor = (View) findViewById(R.id.color_info);

        txtBloque.setText("Bloque : "+temp.getNombreBloque());
        txtNombreSala.setText("Nombre : "+temp.getNombre());
        txtCapacidad.setText("Capacidad : "+temp.getCapacidad());
        txtDescripcion.setText("Descripcion : "+temp.getDescripcionPrestamo());
        viewColor.setBackgroundColor(Color.parseColor(temp.getColor())); // cambiamos el color


    }
}
