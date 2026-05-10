package com.feria.proyecto.dto;

import jakarta.validation.constraints.Email;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.Size;

public record RegisterCustomerRequest(
        @NotBlank String nombre,
        @NotBlank @Email String email,
        @NotBlank @Size(min = 6, max = 80) String password
) {
}
