package com.feria.proyecto.dto;

import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.NotNull;

import java.time.LocalDate;

public record CreateTicketRequest(
        @NotBlank(message = "nombre es obligatorio")
        String nombre,
        @NotNull(message = "fechaEvento es obligatoria")
        LocalDate fechaEvento
) {
}