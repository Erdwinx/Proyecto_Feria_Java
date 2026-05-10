package com.feria.proyecto.service;

import com.feria.proyecto.model.Customer;
import com.feria.proyecto.security.CustomerClaims;
import io.jsonwebtoken.Claims;
import io.jsonwebtoken.JwtException;
import io.jsonwebtoken.Jwts;
import io.jsonwebtoken.SignatureAlgorithm;
import io.jsonwebtoken.security.Keys;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Service;

import java.nio.charset.StandardCharsets;
import java.security.Key;
import java.util.Date;

@Service
public class JwtService {

    private final Key key;
    private final long expirationMillis;

    public JwtService(@Value("${app.jwt.secret}") String secret,
                      @Value("${app.jwt.expiration-minutes:720}") long expirationMinutes) {
        this.key = Keys.hmacShaKeyFor(secret.getBytes(StandardCharsets.UTF_8));
        long safeMinutes = Math.max(5, expirationMinutes);
        this.expirationMillis = safeMinutes * 60_000L;
    }

    public String createToken(Customer customer) {
        Date now = new Date();
        Date expiry = new Date(now.getTime() + expirationMillis);

        return Jwts.builder()
                .setSubject(String.valueOf(customer.getId()))
                .claim("nombre", customer.getNombre())
                .claim("email", customer.getEmail())
                .setIssuedAt(now)
                .setExpiration(expiry)
                .signWith(key, SignatureAlgorithm.HS256)
                .compact();
    }

    public CustomerClaims parseToken(String token) {
        try {
            Claims claims = Jwts.parserBuilder()
                    .setSigningKey(key)
                    .build()
                    .parseClaimsJws(token)
                    .getBody();
            Long id = Long.parseLong(claims.getSubject());
            String nombre = claims.get("nombre", String.class);
            String email = claims.get("email", String.class);
            return new CustomerClaims(id, nombre, email);
        } catch (JwtException | IllegalArgumentException ex) {
            return null;
        }
    }
}
