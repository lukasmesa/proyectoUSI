package com.appusi.usiunidaddeserviciosinformaticos;

import android.app.ProgressDialog;
import android.content.Intent;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.util.Log;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.Toast;

import com.appusi.usiunidaddeserviciosinformaticos.Adapters.AdaptadorSalas;
import com.appusi.usiunidaddeserviciosinformaticos.Clases.Sala;

import com.loopj.android.http.*;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;

import cz.msebera.android.httpclient.Header;

public class SalasActivity extends AppCompatActivity {

    private RecyclerView recyclerView;

    private RecyclerView.LayoutManager layoutManager;

    private ArrayList<Sala> informacionSalas;
    private RecyclerView.Adapter myAdaptador;

    private Button buscar_sala;
    private ProgressDialog dialog;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_salas);


        informacionSalas = new ArrayList<>();

        dialog = new ProgressDialog(this);
        dialog.setIndeterminate(true);
        dialog.setCancelable(false);
        dialog.setMessage("Cargando.. Espere por favor...");
        consumir();



        buscar_sala = (Button) findViewById(R.id.btn_buscar_sala);

        buscar_sala.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Toast.makeText(SalasActivity.this,"Aun no esta implementado",Toast.LENGTH_SHORT).show();

            }
        });

    }


    private ArrayList<Sala> ejemploLlenar(){
        return new ArrayList<Sala>(){{
           add(new Sala("Sala J","C",20,"CLase de seguridad", "#F2F2F2"));
           add(new Sala("Sala I","C",20,"CLase de ING.Software", "#F2F2F3"));
        }};
    }


    public void consumir(){
        AsyncHttpClient client;
        client = new AsyncHttpClient();
        dialog.show();
        client.get("http://dijansoft.xyz/usiWS/index.php?accion=todasprogramadas", new TextHttpResponseHandler() {


            @Override
            public void onFailure(int statusCode, Header[] headers, String responseString, Throwable throwable) {

            }

            @Override
            public void onSuccess(int statusCode, Header[] headers, String responseString) {


                try{
                    Log.d("Respuesta",responseString);
                    JSONArray respuestaJson = new JSONArray(responseString);

                    for (int i = 0; i < respuestaJson.length(); i++) {
                        JSONObject temp = (JSONObject) respuestaJson.get(i);
                        Sala tempSalita  = new Sala(temp.getString("nombre_sala"),
                                temp.getString("nombre_bloque"),
                                temp.getInt("capacidad"),
                                temp.getString("descripcion"),
                                temp.getString("color"));

                        informacionSalas.add(tempSalita);
                    }
                    dialog.dismiss();
                    construirRecyclerView(informacionSalas);
                }catch(JSONException e){
                    Log.e("ERROR en json",e.toString());
                }

            }
        });
    }

    public void construirRecyclerView(ArrayList<Sala> infoSala){
        //Inicialización RecyclerView
        recyclerView = (RecyclerView) findViewById(R.id.my_recycler_view);
        layoutManager = new LinearLayoutManager(this);



        myAdaptador = new AdaptadorSalas(infoSala, R.layout.item_salas, new AdaptadorSalas.OnItemListener() {
            @Override
            public void OnItemClick(Sala salita, int position) {
                //Toast.makeText(SalasActivity.this, salita.getNombre() + " - " + position, Toast.LENGTH_SHORT).show();
                Intent intent = new Intent(SalasActivity.this,ActivityDetallesSalas.class);
                intent.putExtra("sala", salita);
                startActivity(intent);
            }
        });

        recyclerView.setAdapter(myAdaptador);
        recyclerView.setLayoutManager(layoutManager);


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
