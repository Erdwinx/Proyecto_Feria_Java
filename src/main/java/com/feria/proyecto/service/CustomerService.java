package com.feria.proyecto.service;

import com.feria.proyecto.dto.AuthResponse;
import com.feria.proyecto.dto.CustomerResponse;
import com.feria.proyecto.dto.LoginCustomerRequest;
import com.feria.proyecto.dto.RegisterCustomerRequest;
import com.feria.proyecto.model.Customer;
import com.feria.proyecto.repository.CustomerRepository;
import org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder;
import org.springframework.stereotype.Service;

import java.util.Optional;

@Service
public class CustomerService {

    private final CustomerRepository customerRepository;
    private final JwtService jwtService;
    private final BCryptPasswordEncoder passwordEncoder = new BCryptPasswordEncoder();

    public CustomerService(CustomerRepository customerRepository, JwtService jwtService) {
        this.customerRepository = customerRepository;
        this.jwtService = jwtService;
    }

    public AuthResponse register(RegisterCustomerRequest request) {
        String email = request.email().trim().toLowerCase();
        Optional<Customer> existing = customerRepository.findByEmailIgnoreCase(email);
        if (existing.isPresent()) {
            throw new IllegalStateException("Correo ya registrado");
        }

        String hash = passwordEncoder.encode(request.password());
        Customer customer = new Customer(request.nombre().trim(), email, hash);
        Customer saved = customerRepository.save(customer);
        CustomerResponse response = new CustomerResponse(saved.id(), saved.nombre(), saved.email());
        return new AuthResponse(jwtService.createToken(saved), response);
    }

    public AuthResponse login(LoginCustomerRequest request) {
        String email = request.email().trim().toLowerCase();
        Customer customer = customerRepository.findByEmailIgnoreCase(email)
                .orElseThrow(() -> new IllegalStateException("Correo no registrado"));

        if (!passwordEncoder.matches(request.password(), customer.getPasswordHash())) {
            throw new IllegalStateException("Contrasena incorrecta");
        }

        CustomerResponse response = new CustomerResponse(customer.id(), customer.nombre(), customer.email());
        return new AuthResponse(jwtService.createToken(customer), response);
    }
}
