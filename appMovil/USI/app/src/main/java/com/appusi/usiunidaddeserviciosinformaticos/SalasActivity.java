package com.appusi.usiunidaddeserviciosinformaticos;

import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.Toast;

import com.appusi.usiunidaddeserviciosinformaticos.Adapters.AdaptadorSalas;
import com.appusi.usiunidaddeserviciosinformaticos.Clases.SalaInfo;

import java.util.ArrayList;

public class SalasActivity extends AppCompatActivity {

    private RecyclerView recyclerView;

    private ArrayList<SalaInfo> informacionSalas;
    private Button buscar_sala;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_salas);

        //inicialización de la lista de datos de ejemplo
        informacionSalas = new ArrayList<SalaInfo>();

        ejemploLlenar();

        //Inicialización RecyclerView
        recyclerView = (RecyclerView) findViewById(R.id.my_recycler_view);
        recyclerView.setHasFixedSize(true);

        final AdaptadorSalas adaptadorSalas = new AdaptadorSalas(informacionSalas);

        recyclerView.setAdapter(adaptadorSalas);
        recyclerView.setLayoutManager(
                new LinearLayoutManager(this,LinearLayoutManager.VERTICAL,false));

        buscar_sala = (Button) findViewById(R.id.btn_buscar_sala);

        buscar_sala.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Toast.makeText(SalasActivity.this,"Aun no esta implementado",Toast.LENGTH_SHORT).show();
            }
        });

    }


    private void ejemploLlenar(){

        SalaInfo temp3[]= {new SalaInfo("Sala A","Ocupada"),
                new SalaInfo("Sala B","ocupada")};
        añadirSalas("10:00 am - 12:00 pm", temp3);
        SalaInfo temp4[]= {new SalaInfo("Sala F","Ocupada"),
                new SalaInfo("Sala L","disponible")};
        añadirSalas("8:00 am - 10:00 pm", temp4);
        SalaInfo temp[]= {new SalaInfo("Sala A","Ocupada"),
                new SalaInfo("Sala B","disponible")};
        añadirSalas("2:00 pm - 4:00 pm", temp);
        SalaInfo temp2[]= {new SalaInfo("Sala C","Ocupada"),
                new SalaInfo("Sala D","disponible")};
        añadirSalas("4:00 pm - 6:00 pm", temp2);
    }

    private void añadirSalas(String Hora,SalaInfo[] salas){

        informacionSalas.add(new SalaInfo(Hora,""));

        for (SalaInfo i:salas) {
            informacionSalas.add(i);
        }
        informacionSalas.add(new SalaInfo("",""));

    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        MenuInflater inflater = getMenuInflater();
        inflater.inflate(R.menu.menu_salas, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        // Handle item selection
        switch (item.getItemId()) {
            case R.id.menu_pref_sala:
                Toast.makeText(this,"preferencias",Toast.LENGTH_SHORT).show();
                return true;
            case R.id.menu_acer_de_sala:
                Toast.makeText(this,"acerca de",Toast.LENGTH_SHORT).show();
                return true;
            case R.id.menu_filtr_disp_sala:
                Toast.makeText(this,"filtrar por sala disponibles",Toast.LENGTH_SHORT).show();
                return true;
            case R.id.menu_filtr_ocup_sala:
                Toast.makeText(this,"filtrar por salas ocupadas",Toast.LENGTH_SHORT).show();
                return true;
            default:
                return super.onOptionsItemSelected(item);
        }
    }
}
