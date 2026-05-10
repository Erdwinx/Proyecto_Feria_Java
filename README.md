# Prototipo QR dinamico para boletos de feria

Este proyecto en Java (Spring Boot) genera y valida QR dinamicos ligados a:

- ID del boleto
- Nombre del titular
- Ventana temporal de 4 minutos

El QR cambia automaticamente cada 4 minutos y el escaner solo acepta el QR vigente en ese momento.

## Requisitos

- Java 17+
- Maven 3.9+

## Ejecutar en localhost

```bash
mvn spring-boot:run
```

Abrir:

- Emisor: http://localhost:8080/emisor
- Escaner: http://localhost:8080/scanner

## Endpoints principales

- `GET /api/tickets` -> lista de boletos de prueba (id + nombre)
- `GET /api/tickets/{id}/current-qr` -> QR actual para ese boleto
- `POST /api/scan/validate` -> valida si el QR escaneado es vigente y autentico

Body para validar:

```json
{
  "qrText": "FERIAQR|..."
}
```

## Donde cambiar a base de datos

Actualmente los boletos estan en memoria en:

- `src/main/java/com/feria/proyecto/repository/TicketRepository.java`

Para migrar a BD:

1. Reemplaza `TicketRepository` por un repositorio real (JPA, JDBC, etc.).
2. Mantiene los metodos `findAll()` y `findById(String id)` para no romper el resto del flujo.
3. Conserva el mismo modelo `Ticket` (`id`, `nombre`).

## Nota de seguridad para produccion

Configura una clave secreta fuerte para firmar el QR, por ejemplo via variable de entorno:

```bash
set APP_QR_SECRET=tu_clave_super_segura
```

Y en `application.properties` puedes mapearla si luego deseas usar perfiles de entorno.