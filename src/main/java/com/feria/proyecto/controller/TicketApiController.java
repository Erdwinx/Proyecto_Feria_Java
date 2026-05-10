package com.feria.proyecto.controller;

import com.feria.proyecto.dto.CreateTicketRequest;
import com.feria.proyecto.dto.CurrentQrResponse;
import com.feria.proyecto.dto.RecoverTicketRequest;
import com.feria.proyecto.dto.ScanValidationRequest;
import com.feria.proyecto.dto.ScanValidationResponse;
import com.feria.proyecto.model.ScanLogEntry;
import com.feria.proyecto.model.Ticket;
import com.feria.proyecto.repository.TicketRepository;
import com.feria.proyecto.service.DynamicQrService;
import jakarta.validation.Valid;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.http.HttpStatus;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;
import org.springframework.web.server.ResponseStatusException;

import java.util.List;

@RestController
@RequestMapping("/api")
public class TicketApiController {

    private final TicketRepository ticketRepository;
    private final DynamicQrService dynamicQrService;
    private final String recoverKey;

    public TicketApiController(TicketRepository ticketRepository,
                               DynamicQrService dynamicQrService,
                               @Value("${app.recover.key:RECUPERAR-2026}") String recoverKey) {
        this.ticketRepository = ticketRepository;
        this.dynamicQrService = dynamicQrService;
        this.recoverKey = recoverKey;
    }

    @GetMapping("/tickets")
    public List<Ticket> listTickets() {
        return ticketRepository.findAll();
    }

    @GetMapping("/scans")
    public List<ScanLogEntry> listScans() {
        return ticketRepository.listScanLog();
    }

    @PostMapping("/tickets")
    public Ticket createTicket(@Valid @RequestBody CreateTicketRequest request) {
        return ticketRepository.create(request.nombre(), request.fechaEvento());
    }

    @GetMapping("/tickets/{id}")
    public Ticket getTicket(@PathVariable String id) {
        return ticketRepository.findById(id)
                .orElseThrow(() -> new ResponseStatusException(HttpStatus.NOT_FOUND, "Ticket no encontrado"));
    }

    @GetMapping("/tickets/{id}/current-qr")
    public CurrentQrResponse getCurrentQr(@PathVariable String id) {
        Ticket ticket = ticketRepository.findById(id)
                .orElseThrow(() -> new ResponseStatusException(HttpStatus.NOT_FOUND, "Ticket no encontrado"));

        if (ticket.escaneado()) {
            throw new ResponseStatusException(HttpStatus.CONFLICT, "El boleto ya fue escaneado");
        }

        return new CurrentQrResponse(
                ticket.id(),
                ticket.nombre(),
                dynamicQrService.createCurrentQrText(ticket),
                dynamicQrService.currentWindowExpiresAtEpochSeconds()
        );
    }

    @PostMapping("/scan/validate")
    public ScanValidationResponse validateScan(@Valid @RequestBody ScanValidationRequest request) {
        return dynamicQrService.validate(request.qrText());
    }

    @PostMapping("/scan/recover")
    public Ticket recoverTicket(@Valid @RequestBody RecoverTicketRequest request) {
        if (!recoverKey.equals(request.key())) {
            throw new ResponseStatusException(HttpStatus.UNAUTHORIZED, "Clave invalida");
        }

        Ticket ticket = ticketRepository.findById(request.ticketId())
                .orElseThrow(() -> new ResponseStatusException(HttpStatus.NOT_FOUND, "Ticket no encontrado"));

        if (!ticket.escaneado()) {
            return ticket;
        }

        ticket.setEscaneado(false);
        return ticketRepository.save(ticket);
    }
}