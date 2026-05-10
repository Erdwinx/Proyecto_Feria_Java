package com.feria.proyecto.service;

import com.feria.proyecto.dto.ScanValidationResponse;
import com.feria.proyecto.model.Ticket;
import com.feria.proyecto.repository.TicketRepository;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Service;

import javax.crypto.Mac;
import javax.crypto.spec.SecretKeySpec;
import java.nio.charset.StandardCharsets;
import java.security.GeneralSecurityException;
import java.security.MessageDigest;
import java.time.Instant;
import java.util.Base64;
import java.util.Optional;

@Service
public class DynamicQrService {

    private static final String PREFIX = "FERIAQR";
    private static final long STATIC_ISSUED_AT = 0L;
    private static final String STATIC_NONCE = "STATIC";

    private final TicketRepository ticketRepository;
    private final byte[] signingSecret;
    private final long windowSeconds;

    public DynamicQrService(
            TicketRepository ticketRepository,
            @Value("${app.qr.secret:CAMBIAR_EN_PRODUCCION}") String secret,
            @Value("${app.qr.window-seconds:240}") long windowSeconds
    ) {
        this.ticketRepository = ticketRepository;
        this.signingSecret = secret.getBytes(StandardCharsets.UTF_8);
        this.windowSeconds = Math.max(1, windowSeconds);
    }

    public String createCurrentQrText(Ticket ticket) {
        if (ticket.escaneado()) {
            throw new IllegalStateException("El boleto ya fue escaneado");
        }

        long issuedAt = STATIC_ISSUED_AT;
        String nonce = STATIC_NONCE;
        String encodedName = Base64.getUrlEncoder().withoutPadding()
                .encodeToString(ticket.nombre().getBytes(StandardCharsets.UTF_8));
        String signature = sign(ticket.id(), ticket.nombre(), issuedAt, nonce);
        return String.join("|", PREFIX, ticket.id(), encodedName, String.valueOf(issuedAt), nonce, signature);
    }

    public long currentWindowExpiresAtEpochSeconds() {
        return 0L;
    }

    public ScanValidationResponse validate(String qrText) {
        long evaluatedAt = Instant.now().getEpochSecond();

        if (qrText == null || qrText.isBlank()) {
            return new ScanValidationResponse(false, "QR vacio", null, null, evaluatedAt, null);
        }

        String[] parts = qrText.split("\\|");
        if (parts.length != 6 || !PREFIX.equals(parts[0])) {
            return new ScanValidationResponse(false, "Formato de QR invalido", null, null, evaluatedAt, null);
        }

        String ticketId = parts[1];
        String encodedName = parts[2];
        String issuedAtRaw = parts[3];
        String nonce = parts[4];
        String signature = parts[5];

        long issuedAt;
        try {
            issuedAt = Long.parseLong(issuedAtRaw);
        } catch (NumberFormatException ex) {
            return new ScanValidationResponse(false, "Tiempo de emision invalido", ticketId, null, evaluatedAt, null);
        }

        String decodedName;
        try {
            byte[] decoded = Base64.getUrlDecoder().decode(encodedName);
            decodedName = new String(decoded, StandardCharsets.UTF_8);
        } catch (IllegalArgumentException ex) {
            return new ScanValidationResponse(false, "Nombre codificado invalido", ticketId, null, evaluatedAt, null);
        }

        Optional<Ticket> optionalTicket = ticketRepository.findById(ticketId);
        if (optionalTicket.isEmpty()) {
            return new ScanValidationResponse(false, "ID no encontrado", ticketId, decodedName, evaluatedAt, null);
        }

        Ticket ticket = optionalTicket.get();
        if (ticket.escaneado()) {
            return new ScanValidationResponse(false, "Boleto ya escaneado", ticketId, decodedName, evaluatedAt, null);
        }

        if (!ticket.nombre().equals(decodedName)) {
            return new ScanValidationResponse(false, "Nombre no coincide con el ID", ticketId, decodedName, evaluatedAt, null);
        }

        Long expiresAt = null;
        if (issuedAt > 0 && windowSeconds > 0) {
            long computedExpiresAt = issuedAt + windowSeconds;
            if (evaluatedAt > computedExpiresAt) {
                return new ScanValidationResponse(false, "QR vencido. Genera uno nuevo", ticketId, decodedName, evaluatedAt, computedExpiresAt);
            }
            expiresAt = computedExpiresAt;
        }

        String expectedSignature = sign(ticketId, decodedName, issuedAt, nonce);
        if (!MessageDigest.isEqual(
                signature.getBytes(StandardCharsets.UTF_8),
                expectedSignature.getBytes(StandardCharsets.UTF_8)
        )) {
            return new ScanValidationResponse(false, "Firma invalida", ticketId, decodedName, evaluatedAt, null);
        }

        boolean marked = ticketRepository.markAsScanned(ticketId, evaluatedAt);
        if (!marked) {
            return new ScanValidationResponse(false, "Boleto ya escaneado", ticketId, decodedName, evaluatedAt, null);
        }

        return new ScanValidationResponse(true, "Escaneado con exito", ticketId, decodedName, evaluatedAt, expiresAt);
    }

    private String sign(String ticketId, String nombre, long issuedAt, String nonce) {
        try {
            String payload = ticketId + "|" + nombre + "|" + issuedAt + "|" + nonce;
            Mac mac = Mac.getInstance("HmacSHA256");
            mac.init(new SecretKeySpec(signingSecret, "HmacSHA256"));
            byte[] digest = mac.doFinal(payload.getBytes(StandardCharsets.UTF_8));
            return Base64.getUrlEncoder().withoutPadding().encodeToString(digest);
        } catch (GeneralSecurityException ex) {
            throw new IllegalStateException("No se pudo firmar el QR", ex);
        }
    }
}