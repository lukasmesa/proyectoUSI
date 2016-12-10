package com.appusi.usiunidaddeserviciosinformaticos;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.os.AsyncTask;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import com.appusi.usiunidaddeserviciosinformaticos.Clases.Monitores;
import com.google.gson.Gson;
import com.nostra13.universalimageloader.core.DisplayImageOptions;
import com.nostra13.universalimageloader.core.ImageLoader;
import com.nostra13.universalimageloader.core.ImageLoaderConfiguration;
import com.nostra13.universalimageloader.core.assist.FailReason;
import com.nostra13.universalimageloader.core.listener.ImageLoadingListener;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;
import java.util.List;

public class Monitorias extends AppCompatActivity {

    private final String URL_SERVIDOR = "https://dijansoft.xyz/usiWS/index.php?accion=monitorias&orden=True";
    private ListView lstMonitor;
    private ProgressDialog dialog;
    //http://dijansoft.xyz/usiWS/index.php?accion=monitorias&orden=True
    //http://192.168.1.29/Ing_Software/texto.txt

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_monitorias);
        setTitle("MONITORIAS");


        dialog = new ProgressDialog(this);
        dialog.setIndeterminate(true);
        dialog.setCancelable(false);
        dialog.setMessage("Cargando.. Espere por favor...");
        // se Crea una opcion predeterminada
        //  displayImage(...) mientras se establece coneccion y se procesan los datos
        DisplayImageOptions defaultOptions = new DisplayImageOptions.Builder()
                .cacheInMemory(true)
                .cacheOnDisk(true)
                .build();
        ImageLoaderConfiguration config = new ImageLoaderConfiguration.Builder(getApplicationContext())
                .defaultDisplayImageOptions(defaultOptions)
                .build();
        ImageLoader.getInstance().init(config); // Do it on Application start

        lstMonitor = (ListView)findViewById(R.id.lstMonitores);


        // Para empezar a buscar los datos cuando se inicia la aplicación, iniciar la tarea asíncrona.
        new JSONTask().execute(URL_SERVIDOR);

    }

    public class JSONTask extends AsyncTask<String,String, List<Monitores> > {

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            dialog.show();
        }

        @Override
        protected List<Monitores> doInBackground(String... params) {
            HttpURLConnection connection = null;
            BufferedReader reader = null;

            try {
                URL url = new URL(params[0]);
                connection = (HttpURLConnection) url.openConnection();
                connection.connect();
                InputStream stream = connection.getInputStream();
                reader = new BufferedReader(new InputStreamReader(stream));
                StringBuffer buffer = new StringBuffer();
                String line ="";
                while ((line = reader.readLine()) != null){
                    buffer.append(line);
                }

                String finalJson = buffer.toString();

                //TRANSFORMA LA CADENA A VECTOR
                JSONArray Vector = new JSONArray(finalJson);

                List<Monitores> Lista = new ArrayList<>();

                for (int i = 0; i < Vector.length(); i++) {
                    String aux = Vector.get(i).toString();


                    JSONObject obj = new JSONObject(aux);
                    String Hora = obj.getString("hora");
                    JSONArray arr2 = obj.getJSONArray("estudiante");

                    for (int j = 0; j < arr2.length(); j++) {
                        String tem = arr2.get(j).toString();
                        JSONObject obj2 = new JSONObject(tem);
                        //Log.i("Informacion=> ",Hora+" "+obj2.getString("sala")+" "+obj2.getString("codigo")+" "+obj2.getString("nombre")+" "+obj2.getString("apellido"));
                        Monitores Modelo = new Monitores(Hora,obj2.getString("sala"),obj2.getString("descripcion"),obj2.getString("capacidad"),obj2.getString("codigo"),obj2.getString("nombre"),obj2.getString("apellido"),obj2.getString("correo"));
                        Hora = " ";
                        //Agrega cada Monitor a la Lista
                        Lista.add(Modelo);
                    }

                }

                return Lista;

            } catch (MalformedURLException e) {
                e.printStackTrace();
            } catch (IOException e) {
                e.printStackTrace();
            } catch (JSONException e) {
                e.printStackTrace();
            } finally {
                if(connection != null) {
                    connection.disconnect();
                }
                try {
                    if(reader != null) {
                        reader.close();
                    }
                } catch (IOException e) {
                    e.printStackTrace();
                }
            }
            return  null;
        }

        @Override
        protected void onPostExecute(final List<Monitores> result) {
            super.onPostExecute(result);
            dialog.dismiss();
            if(result != null) {

                MovieAdapter adapter = new MovieAdapter(getApplicationContext(), R.layout.row, result);
                lstMonitor.setAdapter(adapter);
                lstMonitor.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                    @Override
                    public void onItemClick(AdapterView<?> parent, View view, int position, long id) {

                        Monitores Monitores = result.get(position);
                        Intent intent = new Intent(Monitorias.this, Detalles.class);
                        intent.putExtra("mostrar_detalles", new Gson().toJson(Monitores));
                        startActivity(intent);
                        //Toast.makeText(getApplicationContext(), "Click en la imagen", Toast.LENGTH_SHORT).show();
                    }
                });
            } else {
                Toast.makeText(getApplicationContext(), "Servidor No Encontrado !!!.", Toast.LENGTH_SHORT).show();
            }
        }
    }

    public class MovieAdapter extends ArrayAdapter {

        private List<Monitores> Lista;
        private int resource;
        private LayoutInflater inflater;
        public MovieAdapter(Context context, int resource, List<Monitores> objects) {
            super(context, resource, objects);
            Lista = objects;
            this.resource = resource;
            inflater = (LayoutInflater) getSystemService(LAYOUT_INFLATER_SERVICE);
        }

        @Override
        public View getView(int position, View convertView, ViewGroup parent) {

            Contenedor holder = null;

            if(convertView == null){
                holder = new Contenedor();
                convertView = inflater.inflate(resource, null);
                holder.lvIcono = (ImageView)convertView.findViewById(R.id.ivIcono);
                holder.txtSala = (TextView)convertView.findViewById(R.id.txtSala);
                holder.txtCodigo = (TextView)convertView.findViewById(R.id.txtCodigo);
                holder.txtNombre = (TextView)convertView.findViewById(R.id.txtNombre);
                holder.txtApellido = (TextView)convertView.findViewById(R.id.txtApellido);
                holder.txtHora = (TextView)convertView.findViewById(R.id.txtHora);
                convertView.setTag(holder);
            } else {
                holder = (Contenedor) convertView.getTag();
            }

            final ProgressBar progressBar = (ProgressBar)convertView.findViewById(R.id.progressBar);

            //
            //Lista.get(position).getImagen()
            //Log.i("Informacion=>",Lista.get(position).getImagen()+" - "+ holder.lvIcono);
            ImageLoader.getInstance().displayImage(Lista.get(position).getImagen(), holder.lvIcono, new ImageLoadingListener() {

                @Override
                public void onLoadingStarted(String imageUri, View view) {
                    progressBar.setVisibility(View.VISIBLE);
                }

                @Override
                public void onLoadingFailed(String imageUri, View view, FailReason failReason) {
                    progressBar.setVisibility(View.GONE);
                }

                @Override
                public void onLoadingComplete(String imageUri, View view, Bitmap loadedImage) {
                    Log.i("Informacion=>",view.getClass().toString());
                    progressBar.setVisibility(View.GONE);
                }

                @Override
                public void onLoadingCancelled(String imageUri, View view) {
                    progressBar.setVisibility(View.GONE);
                }
            });

            holder.txtSala.setText(Lista.get(position).getSala());
            holder.txtCodigo.setText(Lista.get(position).getCodigo());
            holder.txtNombre.setText(Lista.get(position).getNombre());
            holder.txtApellido.setText(Lista.get(position).getApellido());
            holder.txtHora.setText(Lista.get(position).getHora());

            return convertView;
        }


        class Contenedor{
            private ImageView lvIcono;
            private TextView txtHora;
            private TextView txtSala;
            private TextView txtCodigo;
            private TextView txtNombre;
            private TextView txtApellido;
        }

    }


    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // agregar en  el menú; Esto agrega elementos a la barra de acción si está presente.
        getMenuInflater().inflate(R.menu.menu_main, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        int id = item.getItemId();
        //Descomentar para Recargar la aplicacion con un item en el menu
        /*if (id == R.id.action_refresh) {
            new JSONTask().execute(URL_SERVIDOR);
            return true;
        }*/

        return super.onOptionsItemSelected(item);
    }

}
