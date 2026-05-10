package com.feria.proyecto.dto;

import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.NotNull;

import java.time.LocalDate;

public record PurchaseTicketRequest(
        @NotBlank String nombre,
        @NotNull LocalDate fechaEvento
) {
}
