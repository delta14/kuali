# Flujo de trabajo y versionado

Este repo usa 2 ramas largas:

- **`main`** — siempre estable. Cada punto en el que se mergea algo aquí se
  etiqueta con una versión (ver abajo).
- **`develop`** — rama de integración/pruebas. Aquí se despliega para probar
  en un ambiente antes de pasar algo a `main`.

## Flujo para cada cambio

1. Crear una rama `feature/<nombre-corto>` **a partir de `main`**.

   ```bash
   git checkout main
   git pull
   git checkout -b feature/nombre-del-cambio
   ```

2. Desarrollar y probar localmente (`php artisan test` debe pasar).

3. Cuando el cambio esté terminado y probado localmente, mergear la rama a
   `develop` y probarla en el ambiente de pruebas correspondiente.

   ```bash
   git checkout develop
   git pull
   git merge feature/nombre-del-cambio
   git push origin develop
   ```

4. Si todo sale bien en `develop`, mergear esa misma rama `feature/*` a
   `main` y crear el tag de versión (ver reglas abajo).

   ```bash
   git checkout main
   git pull
   git merge feature/nombre-del-cambio
   git push origin main

   git tag -a vX.Y.Z -m "Descripción corta de la versión"
   git push origin vX.Y.Z
   ```

5. Borrar la rama `feature/*` ya mergeada (local y remota) para mantener el
   repo limpio.

   ```bash
   git branch -d feature/nombre-del-cambio
   git push origin --delete feature/nombre-del-cambio
   ```

Si algo falla en `develop`, se corrige en la misma rama `feature/*` y se
vuelve a mergear a `develop` — `main` no se toca hasta que esté bien.

## Versionado (semver: `MAYOR.MENOR.PARCHE`)

Parte siempre de la versión actual de `main` (hoy: **v1.0.0**).

- **PARCHE** (`1.0.0` → `1.0.1`) — corrección de un bug, sin cambiar
  comportamiento esperado ni agregar nada nuevo.
- **MENOR** (`1.0.x` → `1.1.0`) — nueva funcionalidad que no rompe lo que ya
  existía (ej. variantes de producto, reasignar mesa a un pedido).
- **MAYOR** (`1.x.x` → `2.0.0`) — cambio grande que modifica cómo funciona
  algo ya existente, o que requiere migrar datos/comportamiento de forma
  incompatible con la versión anterior.

Cada tag en `main` debe corresponder a algo que ya se probó en `develop`.
