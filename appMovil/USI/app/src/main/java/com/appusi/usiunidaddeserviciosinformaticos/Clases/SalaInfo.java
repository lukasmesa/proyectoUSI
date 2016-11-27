package com.appusi.usiunidaddeserviciosinformaticos.Clases;

/**
 * Created by brayan on 4/11/16.
 */

public class SalaInfo {

    private String titulo;
    private String subtitulo;
    //private String horasala;

    public SalaInfo(String tit, String sub/*,String hora*/){
        titulo = tit;
        subtitulo = sub;
        //horasala = hora;
    }

    public String getTitulo(){
        return titulo;
    }

    public String getSubtitulo(){
        return subtitulo;
    }

    //public String getHorasala(){ return horasala; }
}
