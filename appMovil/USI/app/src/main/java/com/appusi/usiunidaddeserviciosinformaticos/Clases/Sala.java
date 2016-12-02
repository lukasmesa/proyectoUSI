package com.appusi.usiunidaddeserviciosinformaticos.Clases;

import java.io.Serializable;

/**
 * Created by cvem8165 on 1/12/16.
 */

// la serializamos para usarla como parametro en un intento
public class Sala implements Serializable {
    private String nombre;
    private String nombreBloque;
    private int capacidad;
    private String descripcionPrestamo;
    private String color;


    public Sala(String nombre, String nombreBloque, int capacidad, String descripcionPrestamo, String color) {
        this.nombre = nombre;
        this.nombreBloque = nombreBloque;
        this.capacidad = capacidad;
        this.descripcionPrestamo = descripcionPrestamo;
        this.color = color;
    }

    public Sala() {

    }


    public String getNombre() {
        return nombre;
    }

    public void setNombre(String nombre) {
        this.nombre = nombre;
    }

    public String getNombreBloque() {
        return nombreBloque;
    }

    public void setNombreBloque(String nombreBloque) {
        this.nombreBloque = nombreBloque;
    }

    public int getCapacidad() {
        return capacidad;
    }

    public void setCapacidad(int capacidad) {
        this.capacidad = capacidad;
    }

    public String getDescripcionPrestamo() {
        return descripcionPrestamo;
    }

    public void setDescripcionPrestamo(String descripcionPrestamo) {
        this.descripcionPrestamo = descripcionPrestamo;
    }

    public String getColor() {
        return color;
    }

    public void setColor(String color) {
        this.color = color;
    }



}
