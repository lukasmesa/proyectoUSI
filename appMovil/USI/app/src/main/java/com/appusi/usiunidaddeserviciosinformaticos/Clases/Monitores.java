package com.appusi.usiunidaddeserviciosinformaticos.Clases;

/**
 * Created by Eduard on 13/11/2015.
 */
public class Monitores {

    private String Hora;
    private String Sala;
    private String Descripcion;
    private String Capacidad;
    private String Codigo;
    private String Nombre;
    private String Apellido;
    private String Correo;
    private String imagen;

    public Monitores(String hora, String sala,String descripcion, String capacidad, String codigo, String nombre, String apellido,String correo){
        this.Hora = hora;
        this.Sala = sala;
        this.Descripcion = descripcion;
        this.Capacidad = capacidad;
        this.Codigo = codigo;
        this.Nombre = nombre;
        this.Apellido = apellido;
        this.Correo = correo;
        this.imagen ="https://acad.ucaldas.edu.co/fotos/"+codigo+".jpg";
    }

    public String getHora() {
        return this.Hora;
    }

    public String getSala() { return "Sala: "+this.Sala;    }

    public void setSala(String sala) {
        this.Sala = sala;
    }

    public String getCodigo() {
        return this.Codigo;
    }

    public void setCodigo(String cod) {
        this.Codigo = cod;
    }

    public String getImagen() {
        return this.imagen;
    }

    public void setImagen(String image) {
        this.imagen = image;
    }

    public String getNombre() {
        return this.Nombre;
    }

    public void setNombre(String nom) {
        this.Nombre = nom;
    }

    public String getApellido() {
        return this.Apellido;
    }

    public String getDescripcion() {
        return this.Descripcion;
    }

    public String getCapacidad() {
        return this.Capacidad;
    }

    public void setApellido(String ape) {
        this.Apellido = ape;
    }

}
