package com.feria.proyecto.repository;

import com.feria.proyecto.model.ScanLogEntry;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.List;

public interface ScanLogRepository extends JpaRepository<ScanLogEntry, Long> {
    List<ScanLogEntry> findAllByOrderByScannedAtEpochSecondsDesc();
}
