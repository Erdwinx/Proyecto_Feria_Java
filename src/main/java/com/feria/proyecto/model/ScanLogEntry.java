package com.feria.proyecto.model;

import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.Table;

@Entity
@Table(name = "scan_log")
public class ScanLogEntry {

	@Id
	@GeneratedValue(strategy = GenerationType.IDENTITY)
	private Long id;
	private String ticketId;
	private String nombre;
	private long scannedAtEpochSeconds;

	protected ScanLogEntry() {
	}

	public ScanLogEntry(String ticketId, String nombre, long scannedAtEpochSeconds) {
		this.ticketId = ticketId;
		this.nombre = nombre;
		this.scannedAtEpochSeconds = scannedAtEpochSeconds;
	}

	public Long getId() {
		return id;
	}

	public String getTicketId() {
		return ticketId;
	}

	public String getNombre() {
		return nombre;
	}

	public long getScannedAtEpochSeconds() {
		return scannedAtEpochSeconds;
	}

	public String ticketId() {
		return ticketId;
	}

	public String nombre() {
		return nombre;
	}

	public long scannedAtEpochSeconds() {
		return scannedAtEpochSeconds;
	}
}
