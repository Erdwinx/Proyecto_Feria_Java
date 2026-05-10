package com.feria.proyecto.dto;

import jakarta.validation.constraints.Email;
import jakarta.validation.constraints.NotBlank;

public record LoginCustomerRequest(
        @NotBlank @Email String email,
        @NotBlank String password
) {
}
