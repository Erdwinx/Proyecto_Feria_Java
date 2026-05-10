package com.feria.proyecto.dto;

public record CurrentQrResponse(
        String ticketId,
        String nombre,
        String qrText,
        long expiresAtEpochSeconds
) {
}