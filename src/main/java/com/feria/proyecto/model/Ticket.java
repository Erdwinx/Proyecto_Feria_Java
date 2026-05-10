package com.feria.proyecto.model;

import jakarta.persistence.Entity;
import jakarta.persistence.Id;
import jakarta.persistence.Table;

import java.time.LocalDate;

@Entity
@Table(name = "tickets")
public class Ticket {

    @Id
    private String id;
    private String nombre;
    private LocalDate fechaEvento;
    private boolean escaneado;
    private Long customerId;

    protected Ticket() {
    }

    public Ticket(String id, String nombre, LocalDate fechaEvento, boolean escaneado) {
        this.id = id;
        this.nombre = nombre;
        this.fechaEvento = fechaEvento;
        this.escaneado = escaneado;
        this.customerId = null;
    }

    public Ticket(String id, String nombre, LocalDate fechaEvento, boolean escaneado, Long customerId) {
        this.id = id;
        this.nombre = nombre;
        this.fechaEvento = fechaEvento;
        this.escaneado = escaneado;
        this.customerId = customerId;
    }

    public Ticket(String id, String nombre, LocalDate fechaEvento) {
        this(id, nombre, fechaEvento, false);
    }

    public String getId() {
        return id;
    }

    public String getNombre() {
        return nombre;
    }

    public LocalDate getFechaEvento() {
        return fechaEvento;
    }

    public boolean isEscaneado() {
        return escaneado;
    }

    public Long getCustomerId() {
        return customerId;
    }

    public void setEscaneado(boolean escaneado) {
        this.escaneado = escaneado;
    }

    public String id() {
        return id;
    }

    public String nombre() {
        return nombre;
    }

    public LocalDate fechaEvento() {
        return fechaEvento;
    }

    public boolean escaneado() {
        return escaneado;
    }

    public Long customerId() {
        return customerId;
    }
}