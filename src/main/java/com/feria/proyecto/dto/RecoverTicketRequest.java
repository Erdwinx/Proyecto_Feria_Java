package com.feria.proyecto.dto;

import jakarta.validation.constraints.NotBlank;

public record RecoverTicketRequest(
        @NotBlank String ticketId,
        @NotBlank String key
) {
}
