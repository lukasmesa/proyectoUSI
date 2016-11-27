package com.appusi.usiunidaddeserviciosinformaticos.Clases;

import android.os.AsyncTask;
import android.util.Log;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;

/**
 * Created by EDUARD on 13/11/2016.
 */

public class consumir_web extends AsyncTask<String, String, String> {


    public consumir_web(){

    }

    @Override
    protected String doInBackground(String... Ruta) {
        try{
            JSONArray arr = LeerDatos(Ruta[0]);
            for (int i = 0; i < arr.length(); i++) {
                String aux = arr.get(i).toString();
                JSONObject obj = new JSONObject(aux);
                Log.i("CREATION..Ed",obj.getString("sala")+" "+obj.getString("nombre")+" "+obj.getString("apellido"));

            }

        }
        catch(Exception E){
            //E.printStackTrace();
            Log.d("CREATION","Errorddddd");
        }
        return "";
    }

    public void leer() {
        // TODO code application logic here


    }

    public  JSONArray LeerDatos(String Ruta) throws JSONException{
        try {
            /*Eduard*/
            //obtiene los datos en formato Json  de servicios ofrecidos php

            URL url = new URL(Ruta);

            HttpURLConnection conn = (HttpURLConnection) url.openConnection();
            conn.connect();

            //se obtiene la respuesta del servidor
            BufferedReader rd = new BufferedReader(new InputStreamReader(conn.getInputStream()));
            String linea, tmp = "";
            while ((linea = rd.readLine()) != null) {
                tmp += linea;

            }

            //Log.d("CREATION..Eduar",tmp);
            rd.close();

            return new JSONArray(tmp);

        } catch (Exception ex) {
            ex.printStackTrace();
            return new JSONArray("");
        }
    }

}
