package com.feria.proyecto.dto;

public record ScanValidationResponse(
        boolean valid,
        String message,
        String ticketId,
        String nombre,
        long evaluatedAtEpochSeconds,
        Long expiresAtEpochSeconds
) {
}