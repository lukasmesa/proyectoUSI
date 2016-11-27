package com.appusi.usiunidaddeserviciosinformaticos.Adapters;

import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import com.appusi.usiunidaddeserviciosinformaticos.Clases.SalaInfo;
import com.appusi.usiunidaddeserviciosinformaticos.R;

import java.util.ArrayList;

/**
 * Created by brayan on 4/11/16.
 */

public class AdaptadorSalas
        extends RecyclerView.Adapter<AdaptadorSalas.SalasViewHolder> {

    private ArrayList<SalaInfo> informacionSalas;//ARREGLO CON LOS DATOS -> DE TIPO TITULAR PARA QUE TENGA UN TITULO Y SUBTITULO;

    //...

    //CONSTRUCTOR QUE RECIBE LOS DATOS
    public AdaptadorSalas(ArrayList<SalaInfo> informacionSalas) {
        this.informacionSalas = informacionSalas;
    }

    @Override
    public SalasViewHolder onCreateViewHolder(ViewGroup viewGroup, int viewType) {

        View itemView = LayoutInflater.from(viewGroup.getContext())
                .inflate(R.layout.item_salas, viewGroup, false);

        SalasViewHolder salasViewHolder = new SalasViewHolder(itemView);

        return salasViewHolder;
    }

    @Override
    public void onBindViewHolder(SalasViewHolder salasViewHolder, int pos) {
        SalaInfo item = informacionSalas.get(pos);

        salasViewHolder.bindSalaInfo(item);
    }

    @Override
    public int getItemCount() {
        return informacionSalas.size();
    }

    public static class SalasViewHolder
            extends RecyclerView.ViewHolder {

        private TextView txtTitulo;
        private TextView txtSubtitulo;
        //private TextView txtHoraSala;

        public SalasViewHolder(View itemView) {
            super(itemView);

            txtTitulo = (TextView)itemView.findViewById(R.id.LblTitulo);
            txtSubtitulo = (TextView)itemView.findViewById(R.id.LblSubTitulo);
            //txtHoraSala = (TextView) itemView.findViewById(R.id.LblHoraSala);
        }

        public void bindSalaInfo(SalaInfo t) {
            txtTitulo.setText(t.getTitulo());
            txtSubtitulo.setText(t.getSubtitulo());
            //txtHoraSala.setText(t.getHorasala());
        }
    }

    //...
}