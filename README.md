# EQ-09 · Comprobación de acceso por centro

Laboratorio **ficticio e independiente** para repetir una comprobación HTTP de autorización, sin datos reales ni dependencia del backend del equipo.

- Cuenta de prueba: personal ficticio, activa, rol `administracion`.
- Solicitud fija: `GET /personal/usuarios?centro=C01`.
- C01 asignado → 200.
- C01 retirado y C02 asignado → 403, sin datos protegidos.
- C01 restaurado → 200.

## Ejecutar en Mac

```bash
php -S 127.0.0.1:8000 router.php
```

Abrir `http://127.0.0.1:8000/panel`, usar los botones de asignación y fotografiar la **misma URL** antes y después.

## Pruebas automáticas

```bash
php tests/autorization_test.php
```

También se ejecutan en GitHub Actions. **Esta simulación no demuestra que la policy del backend del equipo esté implementada.**
