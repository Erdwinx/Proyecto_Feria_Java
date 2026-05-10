package com.feria.proyecto.repository;

import com.feria.proyecto.model.Ticket;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.List;

public interface TicketJpaRepository extends JpaRepository<Ticket, String> {
	List<Ticket> findAllByCustomerIdOrderByFechaEventoAsc(Long customerId);
}
