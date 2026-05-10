package com.feria.proyecto.controller;

import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.GetMapping;

@Controller
public class ViewController {

    @GetMapping("/")
    public String home() {
        return "redirect:/login";
    }

    @GetMapping("/boletos")
    public String boletos() {
        return "boletos";
    }

    // `/cliente` removed: landing page is `/inicio` directly

    @GetMapping("/inicio")
    public String inicio() {
        return "inicio";
    }

    @GetMapping("/eventos")
    public String eventos() {
        return "eventos";
    }

    @GetMapping("/noticias")
    public String noticias() {
        return "noticias";
    }

    @GetMapping("/promociones")
    public String promociones() {
        return "promociones";
    }

    @GetMapping("/mis-boletos")
    public String misBoletos() {
        return "mis-boletos";
    }

    @GetMapping("/login")
    public String login() {
        return "login";
    }

    @GetMapping("/comprar")
    public String comprar() {
        return "comprar";
    }

    @GetMapping("/emisor")
    public String emisor() {
        return "redirect:/boletos";
    }

    @GetMapping("/scanner")
    public String scanner() {
        return "scanner";
    }
}