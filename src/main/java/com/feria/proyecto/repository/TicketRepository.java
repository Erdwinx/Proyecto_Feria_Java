package com.feria.proyecto.repository;

import com.feria.proyecto.model.ScanLogEntry;
import com.feria.proyecto.model.Ticket;
import jakarta.annotation.PostConstruct;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

import java.time.LocalDate;
import java.util.List;
import java.util.Locale;
import java.util.Optional;
import java.util.concurrent.atomic.AtomicInteger;

@Repository
public class TicketRepository {

    private final TicketJpaRepository ticketJpaRepository;
    private final ScanLogRepository scanLogRepository;
    private final AtomicInteger nextId = new AtomicInteger(1);

    public TicketRepository(TicketJpaRepository ticketJpaRepository, ScanLogRepository scanLogRepository) {
        this.ticketJpaRepository = ticketJpaRepository;
        this.scanLogRepository = scanLogRepository;
    }

    @PostConstruct
    public void initData() {
        if (ticketJpaRepository.count() == 0) {
            seedData();
        }
        updateNextId();
    }

    private void seedData() {
        List<Ticket> initial = List.of(
                new Ticket("AN00000001", "Ana Perez", LocalDate.of(2026, 5, 10)),
                new Ticket("LU00000002", "Luis Soto", LocalDate.of(2026, 5, 11)),
                new Ticket("MA00000003", "Maria Gomez", LocalDate.of(2026, 5, 12)),
                new Ticket("JO00000004", "Jorge Ruiz", LocalDate.of(2026, 5, 13))
        );
        ticketJpaRepository.saveAll(initial);
    }

    private void updateNextId() {
        int max = ticketJpaRepository.findAll().stream()
                .mapToInt(ticket -> parseNumericId(ticket.id()))
                .max()
                .orElse(0);
        nextId.set(max + 1);
    }

    private int parseNumericId(String id) {
        if (id == null || id.length() < 3) {
            return 0;
        }

        String digits = id.substring(2);
        try {
            return Integer.parseInt(digits);
        } catch (NumberFormatException ex) {
            return 0;
        }
    }

    public List<Ticket> findAll() {
        return ticketJpaRepository.findAll();
    }

    public List<Ticket> findAllByCustomer(Long customerId) {
        if (customerId == null) {
            return List.of();
        }
        return ticketJpaRepository.findAllByCustomerIdOrderByFechaEventoAsc(customerId);
    }

    public Optional<Ticket> findById(String id) {
        return ticketJpaRepository.findById(id);
    }

    public Ticket save(Ticket ticket) {
        return ticketJpaRepository.save(ticket);
    }

    public List<ScanLogEntry> listScanLog() {
        return scanLogRepository.findAllByOrderByScannedAtEpochSecondsDesc();
    }

    @Transactional
    public Ticket create(String nombre, LocalDate fechaEvento) {
        String prefix = buildPrefix(nombre);
        String id = prefix + String.format("%08d", nextId.getAndIncrement());
        Ticket ticket = new Ticket(id, nombre, fechaEvento, false);
        ticketJpaRepository.save(ticket);
        return ticket;
    }

    @Transactional
    public Ticket createForCustomer(Long customerId, String nombre, LocalDate fechaEvento) {
        String prefix = buildPrefix(nombre);
        String id = prefix + String.format("%08d", nextId.getAndIncrement());
        Ticket ticket = new Ticket(id, nombre, fechaEvento, false, customerId);
        ticketJpaRepository.save(ticket);
        return ticket;
    }

    private String buildPrefix(String nombre) {
        if (nombre == null || nombre.isBlank()) {
            return "XX";
        }

        String firstName = nombre.trim().split("\\s+")[0];
        String letters = firstName.replaceAll("[^\\p{L}]", "").toUpperCase(Locale.ROOT);
        if (letters.length() >= 2) {
            return letters.substring(0, 2);
        }
        if (letters.length() == 1) {
            return letters + "X";
        }
        return "XX";
    }

    @Transactional
    public boolean markAsScanned(String id, long scannedAtEpochSeconds) {
        Optional<Ticket> optionalTicket = ticketJpaRepository.findById(id);
        if (optionalTicket.isEmpty()) {
            return false;
        }

        Ticket ticket = optionalTicket.get();
        if (ticket.escaneado()) {
            return false;
        }

        ticket.setEscaneado(true);
        ticketJpaRepository.save(ticket);
        scanLogRepository.save(new ScanLogEntry(ticket.id(), ticket.nombre(), scannedAtEpochSeconds));
        return true;
    }
}
