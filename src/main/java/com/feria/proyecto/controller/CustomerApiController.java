package com.feria.proyecto.controller;

import com.feria.proyecto.dto.AuthResponse;
import com.feria.proyecto.dto.CustomerResponse;
import com.feria.proyecto.dto.LoginCustomerRequest;
import com.feria.proyecto.dto.PurchaseTicketRequest;
import com.feria.proyecto.dto.RegisterCustomerRequest;
import com.feria.proyecto.model.Ticket;
import com.feria.proyecto.repository.TicketRepository;
import com.feria.proyecto.security.CustomerClaims;
import com.feria.proyecto.service.CustomerService;
import jakarta.validation.Valid;
import org.springframework.http.HttpStatus;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;
import org.springframework.web.server.ResponseStatusException;
import org.springframework.security.core.Authentication;

import java.util.List;

@RestController
@RequestMapping("/api/customers")
public class CustomerApiController {

    private final CustomerService customerService;
    private final TicketRepository ticketRepository;

    public CustomerApiController(CustomerService customerService, TicketRepository ticketRepository) {
        this.customerService = customerService;
        this.ticketRepository = ticketRepository;
    }

    @PostMapping("/register")
    public AuthResponse register(@Valid @RequestBody RegisterCustomerRequest request) {
        try {
            return customerService.register(request);
        } catch (IllegalStateException ex) {
            throw new ResponseStatusException(HttpStatus.BAD_REQUEST, ex.getMessage());
        }
    }

    @PostMapping("/login")
    public AuthResponse login(@Valid @RequestBody LoginCustomerRequest request) {
        try {
            return customerService.login(request);
        } catch (IllegalStateException ex) {
            throw new ResponseStatusException(HttpStatus.BAD_REQUEST, ex.getMessage());
        }
    }

    @GetMapping("/me")
    public CustomerResponse me(Authentication authentication) {
        CustomerClaims claims = requireClaims(authentication);
        return new CustomerResponse(claims.id(), claims.nombre(), claims.email());
    }

    @GetMapping("/tickets")
    public List<Ticket> listTickets(Authentication authentication) {
        CustomerClaims claims = requireClaims(authentication);
        return ticketRepository.findAllByCustomer(claims.id());
    }

    @PostMapping("/tickets")
    public Ticket purchase(Authentication authentication,
                           @Valid @RequestBody PurchaseTicketRequest request) {
        CustomerClaims claims = requireClaims(authentication);
        return ticketRepository.createForCustomer(claims.id(), request.nombre(), request.fechaEvento());
    }

    private CustomerClaims requireClaims(Authentication authentication) {
        if (authentication == null || !(authentication.getPrincipal() instanceof CustomerClaims claims)) {
            throw new ResponseStatusException(HttpStatus.UNAUTHORIZED, "No autenticado");
        }
        return claims;
    }
}
