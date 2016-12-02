package com.appusi.usiunidaddeserviciosinformaticos.Adapters;

import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import com.appusi.usiunidaddeserviciosinformaticos.Clases.Sala;
import com.appusi.usiunidaddeserviciosinformaticos.Clases.SalaInfo;
import com.appusi.usiunidaddeserviciosinformaticos.R;

import java.util.ArrayList;

/**
 * Created by brayan on 4/11/16.
 */

public class AdaptadorSalas
        extends RecyclerView.Adapter<AdaptadorSalas.SalasViewHolder> {

    private ArrayList<Sala> informacionSalas;//ARREGLO CON LOS DATOS -> DE TIPO TITULAR PARA QUE TENGA UN TITULO Y SUBTITULO;
    private int layout;
    private OnItemListener itemListener;


    //...

    //CONSTRUCTOR QUE RECIBE LOS DATOS
    public AdaptadorSalas(ArrayList<Sala> informacionSalas, int layout, OnItemListener onItemListener) {
        this.informacionSalas = informacionSalas;
        this.layout = layout;
        this.itemListener = onItemListener;
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
        salasViewHolder.bindSalaInfo(informacionSalas.get(pos), this.itemListener);

    }

    @Override
    public int getItemCount() {
        return informacionSalas.size();
    }

    public static class SalasViewHolder
            extends RecyclerView.ViewHolder {

        private TextView txtTitulo;
        private TextView txtSubtitulo;
        private TextView txtBloque;
        //private TextView txtHoraSala;

        public SalasViewHolder(View itemView) {
            super(itemView);

            txtTitulo = (TextView)itemView.findViewById(R.id.LblTitulo);
            txtSubtitulo = (TextView)itemView.findViewById(R.id.LblSubTitulo);
            txtBloque = (TextView)itemView.findViewById(R.id.LblBloque);
            //txtHoraSala = (TextView) itemView.findViewById(R.id.LblHoraSala);
        }

        public void bindSalaInfo(final Sala t, final OnItemListener listener) {
            txtTitulo.setText(t.getNombre());
            txtSubtitulo.setText(t.getDescripcionPrestamo());
            txtBloque.setText(t.getNombreBloque());

            itemView.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    listener.OnItemClick(t, getAdapterPosition());
                }
            });

            //txtHoraSala.setText(t.getHorasala());
        }
    }

    public interface OnItemListener{
        public void OnItemClick(Sala salita, int position);
    }


    //...
}