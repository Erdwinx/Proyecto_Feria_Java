package com.feria.proyecto.dto;

import jakarta.validation.constraints.NotBlank;

public record ScanValidationRequest(
        @NotBlank(message = "qrText es obligatorio")
        String qrText
) {
}