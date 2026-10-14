# EQ-09 · Explicar y repetir una comprobación de acceso

**Laboratorio ficticio y aislado** para demostrar el cambio de autorización al modificar el centro asignado. No contiene datos reales ni es el backend compartido.

## Ejecutar en macOS

```bash
git pull
php -S 127.0.0.1:8000 router.php
```

Abrir **http://127.0.0.1:8000/panel**.

1. Pulsar **Asignar C01** y abrir **GET /centros/C01/configuracion**.
2. Abrir **Desarrollo → Mostrar inspector web → Red**, recargar la pestaña de la petición, seleccionar `configuracion`: HTTP **200**.
3. Volver al panel y pulsar **Retirar C01 y asignar C02**.
4. Volver a la misma URL **/centros/C01/configuracion**, recargar y seleccionar `configuracion`: HTTP **403** sin `data`.
5. Opcional: restaurar C01 y repetir (HTTP **200**).

La **URL de la petición permanece idéntica**; cambia únicamente la asignación de la cuenta ficticia P10.

## Test automatizado

```bash
php tests/autorizacion_test.php
```

GitHub Actions también ejecuta el test. Las capturas son de Safari con el inspector **Red** y muestran la petición y el estado HTTP.

**Límite:** esto prueba la simulación PHP local, no la policy Laravel real del equipo.
