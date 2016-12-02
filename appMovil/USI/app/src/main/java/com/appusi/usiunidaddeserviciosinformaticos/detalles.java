package com.appusi.usiunidaddeserviciosinformaticos;

import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.widget.TextView;

import com.appusi.usiunidaddeserviciosinformaticos.Clases.Monitores;
import com.google.gson.Gson;

import org.json.JSONException;
import org.json.JSONObject;

public class Detalles extends AppCompatActivity {

    private TextView textnomsala;
    private TextView textnomMon;
    private TextView textCod;
    private TextView textcorreo;
    private TextView textdesc;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_detalles);
        setTitle("MAS DETALLES");


        this.textnomsala = (TextView) findViewById(R.id.textnomsala);
        this.textnomMon = (TextView) findViewById(R.id.textnomMon);
        this.textCod = (TextView) findViewById(R.id.textCod);
        this.textcorreo = (TextView) findViewById(R.id.textcorreo);
        this.textdesc = (TextView) findViewById(R.id.textdesc);

        /*
        if(getSupportActionBar() != null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            getSupportActionBar().setDisplayShowHomeEnabled(true);
        }*/

        // Configuración de vistas de texto y
        //setUpUIViews();


        Bundle bundle = getIntent().getExtras();
        if(bundle != null){
            String json = bundle.getString("mostrar_detalles");

            JSONObject obj = null;
            try {
                obj = new JSONObject(json);
                this.textnomsala.setText(obj.getString("Sala"));
                this.textnomMon.setText(obj.getString("Nombre")+"  "+obj.getString("Apellido"));
                this.textCod.setText(obj.getString("Codigo"));
                this.textcorreo.setText(obj.getString("Correo"));
                this.textdesc.setText(obj.getString("Descripcion"));

            } catch (JSONException e) {
                e.printStackTrace();
            }



        }

    }
}
