package com.almacen.inventario;

import com.almacen.inventario.model.UserAccount;
import com.almacen.inventario.repository.UserAccountRepository;
import org.springframework.boot.CommandLineRunner;
import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.context.annotation.Bean;
import org.springframework.security.crypto.password.PasswordEncoder;

@SpringBootApplication
public class InventarioAplicacion {
    public static void main(String[] args) {
        SpringApplication.run(InventarioAplicacion.class, args);
    }

    // Crear usuario admin al iniciar
    @Bean
    CommandLineRunner initAdmin(UserAccountRepository repo, PasswordEncoder enc){
        return args -> {
            if (repo.findByUsername("admin") == null) {
                UserAccount u = new UserAccount();
                u.setUsername("admin");
                u.setPasswordHash(enc.encode("admin123"));
                u.setRole("ADMIN");
                u.setEnabled(true);
                repo.save(u);
            }
        };
    }
}
