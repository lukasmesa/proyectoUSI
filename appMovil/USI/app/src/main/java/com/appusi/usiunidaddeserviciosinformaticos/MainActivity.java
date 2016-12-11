package com.appusi.usiunidaddeserviciosinformaticos;

import android.content.DialogInterface;
import android.content.Intent;
import android.support.v7.app.AlertDialog;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.RadioButton;
import android.widget.TextView;
import android.widget.Toast;


import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Locale;

public class MainActivity extends AppCompatActivity {



    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        TextView txt_fecha_hoy_date = (TextView) findViewById(R.id.txt_fecha_hoy_date);
        ImageView imgSearchSalas = (ImageView) findViewById(R.id.imgSearchSalas);
        ImageView imgSearchMonitorias = (ImageView) findViewById(R.id.imgSearchMonitorias) ;


        imgSearchSalas.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                AlertDialog.Builder dlgAlert  = new AlertDialog.Builder(MainActivity.this);
                dlgAlert.setMessage("La letra que se encuentra a la izquierda, representa el bloque " +
                        "en el que se encuentra esa sala");
                dlgAlert.setTitle("Atención");
                dlgAlert.setCancelable(false);
                dlgAlert.setPositiveButton("OK", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialogInterface, int i) {
                        Intent intent = new Intent(MainActivity.this,SalasActivity.class);
                        startActivity(intent);
                    }
                });
                dlgAlert.create().show();

            }
        });
        imgSearchMonitorias.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent intent = new Intent(MainActivity.this,Monitorias.class);
                startActivity(intent);
            }
        });



        Date fechaActual = new Date();


        DateFormat dateFormat = new SimpleDateFormat("dd/MM/yyyy", Locale.ROOT);
        String fecha = "" + dateFormat.format(fechaActual);
        txt_fecha_hoy_date.setText(fecha);
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        MenuInflater inflater = getMenuInflater();
        inflater.inflate(R.menu.menu_main, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        // Handle item selection
        switch (item.getItemId()) {
            case R.id.main_pref_menu:
                Toast.makeText(this,"preferencias",Toast.LENGTH_SHORT).show();
                return true;
            case R.id.main_ace_de_menu:
                Toast.makeText(this,"acerca de",Toast.LENGTH_SHORT).show();
                return true;
            default:
                return super.onOptionsItemSelected(item);
        }
    }

}
