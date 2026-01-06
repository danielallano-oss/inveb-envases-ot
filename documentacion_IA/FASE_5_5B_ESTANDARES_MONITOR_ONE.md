# FASE 5.5B: Estandares UI Monitor One para INVEB

**ID**: `PASO-05.05B-V12`
**Fecha**: 2025-12-19
**Estado**: Completado
**Tipo**: Anexo a Fase de Diseno UI
**Fuente**: Azure DevOps - Proyecto Monitor One (web-monitor)

---

## Proposito

Este documento define los estandares de UI extraidos del proyecto Monitor One de Tecnoandina,
adaptados para su implementacion en INVEB Envases-OT. Los patrones originales de React/styled-components
se traducen a Bootstrap 4/CSS variables para compatibilidad con el stack Laravel/Blade.

---

## 1. PALETA DE COLORES CORPORATIVA

### 1.1 Colores Principales

| Nombre | Valor Hex | CSS Variable | Uso |
|--------|-----------|--------------|-----|
| **Primary** | `#003A81` | `--mo-primary` | Botones principales, headers, enlaces |
| **Secondary** | `#EC7126` | `--mo-secondary` | Acciones secundarias, highlights |
| **Open Color (Accent)** | `#05C1CA` | `--mo-accent` | Acentos, bordes activos, iconos |
| **Card Header Banner** | `#01214d` | `--mo-card-header` | Headers de cards y paneles |
| **Corporate** | `#6D7883` | `--mo-corporate` | Texto secundario, iconos neutrales |

### 1.2 Colores de Fondo

| Nombre | Valor Hex | CSS Variable | Uso |
|--------|-----------|--------------|-----|
| **White Background** | `#FFFFFF` | `--mo-bg-white` | Fondo principal |
| **Light Gray** | `#F2F2F2` | `--mo-bg-light` | Fondos alternos |
| **Sidebar** | `#1A1A2E` | `--mo-sidebar-bg` | Fondo sidebar |
| **Blue Light** | `#D1E3F8` | `--mo-bg-blue-light` | Fondos informativos |

### 1.3 Colores de Estado

| Estado | Valor Hex | CSS Variable | Uso |
|--------|-----------|--------------|-----|
| **Success** | `#28A745` | `--mo-success` | Operaciones exitosas |
| **Warning** | `#FFC107` | `--mo-warning` | Advertencias |
| **Danger** | `#DC3545` | `--mo-danger` | Errores, eliminacion |
| **Info** | `#17A2B8` | `--mo-info` | Informacion |
| **Active Green** | `#00E676` | `--mo-active` | Estados activos |
| **Disabled Gray** | `#9E9E9E` | `--mo-disabled` | Elementos deshabilitados |

### 1.4 Colores de Texto

| Nombre | Valor Hex | CSS Variable | Uso |
|--------|-----------|--------------|-----|
| **Text Primary** | `#212529` | `--mo-text-primary` | Texto principal |
| **Text Secondary** | `#6C757D` | `--mo-text-secondary` | Texto secundario |
| **Text Muted** | `#9E9E9E` | `--mo-text-muted` | Texto deshabilitado |
| **Text White** | `#FFFFFF` | `--mo-text-white` | Texto sobre fondos oscuros |
| **Link** | `#003A81` | `--mo-link` | Enlaces |
| **Link Hover** | `#002654` | `--mo-link-hover` | Enlaces hover |

---

## 2. TIPOGRAFIA

### 2.1 Familia de Fuentes

```css
/* Monitor One Typography */
--mo-font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
```

**Importacion Google Fonts**:
```html
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
```

### 2.2 Pesos de Fuente

| Peso | CSS Variable | Uso |
|------|--------------|-----|
| Light (300) | `--mo-font-light` | Subtitulos, texto auxiliar |
| Regular (400) | `--mo-font-regular` | Texto de cuerpo |
| Medium (500) | `--mo-font-medium` | Labels, destacados |
| Semi-Bold (600) | `--mo-font-semibold` | Titulos de seccion |
| Bold (700) | `--mo-font-bold` | Titulos principales |

### 2.3 Tamanos de Fuente

| Elemento | Tamano | CSS Variable |
|----------|--------|--------------|
| H1 | 2rem (32px) | `--mo-h1` |
| H2 | 1.5rem (24px) | `--mo-h2` |
| H3 | 1.25rem (20px) | `--mo-h3` |
| H4 | 1rem (16px) | `--mo-h4` |
| Body | 0.875rem (14px) | `--mo-body` |
| Small | 0.75rem (12px) | `--mo-small` |
| Tiny | 0.625rem (10px) | `--mo-tiny` |

---

## 3. COMPONENTES UI

### 3.1 Botones

**Clases CSS para INVEB**:

```css
/* Boton Primario */
.btn-mo-primary {
    background-color: var(--mo-primary);
    border-color: var(--mo-primary);
    color: var(--mo-text-white);
    font-family: var(--mo-font-family);
    font-weight: 500;
    border-radius: 4px;
    padding: 0.5rem 1.25rem;
    transition: all 0.2s ease;
}

.btn-mo-primary:hover {
    background-color: #002654;
    border-color: #002654;
}

/* Boton Secundario */
.btn-mo-secondary {
    background-color: var(--mo-secondary);
    border-color: var(--mo-secondary);
    color: var(--mo-text-white);
}

/* Boton Outline */
.btn-mo-outline {
    background-color: transparent;
    border: 2px solid var(--mo-primary);
    color: var(--mo-primary);
}

/* Boton Gradiente (para acciones destacadas) */
.btn-mo-gradient {
    background: linear-gradient(135deg, var(--mo-primary) 0%, var(--mo-accent) 100%);
    border: none;
    color: var(--mo-text-white);
}
```

### 3.2 Cards

```css
/* Card Monitor One */
.card-mo {
    border: none;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.card-mo .card-header {
    background-color: var(--mo-card-header);
    color: var(--mo-text-white);
    font-weight: 600;
    border-radius: 8px 8px 0 0;
    padding: 1rem 1.25rem;
}

.card-mo .card-body {
    padding: 1.5rem;
}
```

### 3.3 Tablas (DataTables)

```css
/* Tabla Monitor One */
.table-mo {
    font-family: var(--mo-font-family);
}

.table-mo thead th {
    background-color: var(--mo-primary);
    color: var(--mo-text-white);
    font-weight: 600;
    border: none;
    padding: 0.875rem 1rem;
}

.table-mo tbody tr:hover {
    background-color: var(--mo-bg-blue-light);
}

.table-mo tbody td {
    padding: 0.75rem 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #dee2e6;
}

/* Filas alternadas */
.table-mo tbody tr:nth-child(even) {
    background-color: var(--mo-bg-light);
}
```

### 3.4 Formularios

```css
/* Input Flotante (Floating Label) */
.form-floating-mo {
    position: relative;
    margin-bottom: 1.5rem;
}

.form-floating-mo input,
.form-floating-mo select {
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 0.75rem 1rem;
    font-family: var(--mo-font-family);
    transition: border-color 0.2s ease;
}

.form-floating-mo input:focus,
.form-floating-mo select:focus {
    border-color: var(--mo-accent);
    box-shadow: 0 0 0 3px rgba(5, 193, 202, 0.15);
    outline: none;
}

.form-floating-mo label {
    color: var(--mo-text-secondary);
    font-size: 0.875rem;
    font-weight: 500;
}

/* Select2 Override */
.select2-container--mo .select2-selection--single {
    border: 1px solid #ced4da;
    border-radius: 4px;
    height: 42px;
}

.select2-container--mo .select2-selection--single:focus {
    border-color: var(--mo-accent);
}
```

### 3.5 Sidebar

```css
/* Sidebar Monitor One */
.sidebar-mo {
    background-color: var(--mo-sidebar-bg);
    min-height: 100vh;
    width: 250px;
}

.sidebar-mo .nav-link {
    color: rgba(255, 255, 255, 0.7);
    padding: 0.75rem 1.25rem;
    font-family: var(--mo-font-family);
    transition: all 0.2s ease;
}

.sidebar-mo .nav-link:hover {
    color: var(--mo-text-white);
    background-color: rgba(255, 255, 255, 0.1);
}

.sidebar-mo .nav-link.active {
    color: var(--mo-accent);
    background-color: rgba(5, 193, 202, 0.1);
    border-left: 3px solid var(--mo-accent);
}

.sidebar-mo .nav-section-title {
    color: var(--mo-corporate);
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 1rem 1.25rem 0.5rem;
}
```

### 3.6 Tabs de Navegacion

```css
/* NavTab Monitor One */
.nav-tabs-mo {
    border-bottom: 2px solid #dee2e6;
}

.nav-tabs-mo .nav-link {
    border: none;
    color: var(--mo-text-secondary);
    font-weight: 500;
    padding: 0.75rem 1.5rem;
    margin-bottom: -2px;
    transition: all 0.2s ease;
}

.nav-tabs-mo .nav-link:hover {
    color: var(--mo-primary);
}

.nav-tabs-mo .nav-link.active {
    color: var(--mo-primary);
    border-bottom: 3px solid var(--mo-accent);
    background: transparent;
}
```

---

## 4. ESPACIADO Y LAYOUT

### 4.1 Sistema de Espaciado

| Variable | Valor | Uso |
|----------|-------|-----|
| `--mo-space-xs` | 0.25rem (4px) | Espaciado minimo |
| `--mo-space-sm` | 0.5rem (8px) | Espaciado pequeno |
| `--mo-space-md` | 1rem (16px) | Espaciado estandar |
| `--mo-space-lg` | 1.5rem (24px) | Espaciado grande |
| `--mo-space-xl` | 2rem (32px) | Espaciado extra grande |

### 4.2 Border Radius

| Variable | Valor | Uso |
|----------|-------|-----|
| `--mo-radius-sm` | 4px | Botones, inputs |
| `--mo-radius-md` | 8px | Cards, paneles |
| `--mo-radius-lg` | 12px | Modales |
| `--mo-radius-full` | 50% | Avatares, badges |

### 4.3 Sombras

```css
--mo-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
--mo-shadow-md: 0 2px 8px rgba(0, 0, 0, 0.1);
--mo-shadow-lg: 0 4px 16px rgba(0, 0, 0, 0.12);
--mo-shadow-hover: 0 4px 12px rgba(0, 58, 129, 0.15);
```

---

## 5. ARCHIVO CSS VARIABLES COMPLETO

```css
/* ============================================
   MONITOR ONE DESIGN SYSTEM - INVEB
   Generado: 2025-12-19
   Fuente: Azure DevOps - web-monitor
   ============================================ */

:root {
    /* Colores Principales */
    --mo-primary: #003A81;
    --mo-secondary: #EC7126;
    --mo-accent: #05C1CA;
    --mo-card-header: #01214d;
    --mo-corporate: #6D7883;

    /* Fondos */
    --mo-bg-white: #FFFFFF;
    --mo-bg-light: #F2F2F2;
    --mo-sidebar-bg: #1A1A2E;
    --mo-bg-blue-light: #D1E3F8;

    /* Estados */
    --mo-success: #28A745;
    --mo-warning: #FFC107;
    --mo-danger: #DC3545;
    --mo-info: #17A2B8;
    --mo-active: #00E676;
    --mo-disabled: #9E9E9E;

    /* Texto */
    --mo-text-primary: #212529;
    --mo-text-secondary: #6C757D;
    --mo-text-muted: #9E9E9E;
    --mo-text-white: #FFFFFF;
    --mo-link: #003A81;
    --mo-link-hover: #002654;

    /* Tipografia */
    --mo-font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --mo-font-light: 300;
    --mo-font-regular: 400;
    --mo-font-medium: 500;
    --mo-font-semibold: 600;
    --mo-font-bold: 700;

    /* Tamanos de Fuente */
    --mo-h1: 2rem;
    --mo-h2: 1.5rem;
    --mo-h3: 1.25rem;
    --mo-h4: 1rem;
    --mo-body: 0.875rem;
    --mo-small: 0.75rem;
    --mo-tiny: 0.625rem;

    /* Espaciado */
    --mo-space-xs: 0.25rem;
    --mo-space-sm: 0.5rem;
    --mo-space-md: 1rem;
    --mo-space-lg: 1.5rem;
    --mo-space-xl: 2rem;

    /* Border Radius */
    --mo-radius-sm: 4px;
    --mo-radius-md: 8px;
    --mo-radius-lg: 12px;
    --mo-radius-full: 50%;

    /* Sombras */
    --mo-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
    --mo-shadow-md: 0 2px 8px rgba(0, 0, 0, 0.1);
    --mo-shadow-lg: 0 4px 16px rgba(0, 0, 0, 0.12);
    --mo-shadow-hover: 0 4px 12px rgba(0, 58, 129, 0.15);

    /* Transiciones */
    --mo-transition: all 0.2s ease;
}
```

---

## 6. IMPLEMENTACION EN BLADE

### 6.1 Layout Base Modificado

```blade
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    {{-- Fuente Poppins --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- CSS Variables Monitor One --}}
    <link href="{{ asset('css/monitor-one-variables.css') }}" rel="stylesheet">

    {{-- Bootstrap + Custom --}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="font-mo">
    {{-- Sidebar --}}
    <nav class="sidebar-mo">
        @include('partials.sidebar')
    </nav>

    {{-- Content --}}
    <main class="main-content">
        @yield('content')
    </main>
</body>
</html>
```

### 6.2 Ejemplo Card OT

```blade
{{-- Componente Card con estilos Monitor One --}}
<div class="card card-mo">
    <div class="card-header">
        <i class="fas fa-clipboard-list mr-2"></i>
        Ordenes de Trabajo
    </div>
    <div class="card-body">
        <table class="table table-mo" id="dataTableOT">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ordenes as $ot)
                <tr>
                    <td>{{ $ot->id }}</td>
                    <td>{{ $ot->cliente->razon_social }}</td>
                    <td>
                        <span class="badge" style="background-color: {{ $ot->estado->color }}">
                            {{ $ot->estado->nombre }}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-mo-primary btn-sm">Ver</button>
                        <button class="btn btn-mo-outline btn-sm">Editar</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
```

---

## 7. MAPEO BOOTSTRAP -> MONITOR ONE

### 7.1 Clases de Bootstrap a Sobreescribir

| Bootstrap Original | Monitor One Override |
|--------------------|---------------------|
| `.btn-primary` | `.btn-mo-primary` |
| `.btn-secondary` | `.btn-mo-secondary` |
| `.card` | `.card-mo` |
| `.table` | `.table-mo` |
| `.nav-tabs` | `.nav-tabs-mo` |
| `.form-control` | `.form-control-mo` |
| `.badge-primary` | `.badge-mo-primary` |

### 7.2 Override Global (Opcional)

```css
/* Para aplicar Monitor One a todo Bootstrap */
.btn-primary {
    background-color: var(--mo-primary) !important;
    border-color: var(--mo-primary) !important;
}

.btn-secondary {
    background-color: var(--mo-secondary) !important;
    border-color: var(--mo-secondary) !important;
}

body {
    font-family: var(--mo-font-family) !important;
}
```

---

## 8. ICONOGRAFIA

### 8.1 Libreria de Iconos

Monitor One utiliza **Font Awesome 5** para iconos.

```html
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
```

### 8.2 Iconos Comunes

| Accion | Icono | Clase |
|--------|-------|-------|
| Crear | Plus | `fas fa-plus` |
| Editar | Pencil | `fas fa-edit` |
| Eliminar | Trash | `fas fa-trash` |
| Ver | Eye | `fas fa-eye` |
| Descargar | Download | `fas fa-download` |
| Buscar | Search | `fas fa-search` |
| Configuracion | Cog | `fas fa-cog` |
| Usuario | User | `fas fa-user` |
| OT | Clipboard | `fas fa-clipboard-list` |
| Reporte | Chart | `fas fa-chart-bar` |

---

## 9. RESPONSIVE BREAKPOINTS

```css
/* Breakpoints Monitor One */
--mo-breakpoint-xs: 0;
--mo-breakpoint-sm: 576px;
--mo-breakpoint-md: 768px;
--mo-breakpoint-lg: 992px;
--mo-breakpoint-xl: 1200px;
--mo-breakpoint-xxl: 1400px;
```

---

## 10. RESUMEN DE IMPLEMENTACION

### 10.1 Pasos para Aplicar

1. **Agregar Google Font Poppins** al layout base
2. **Crear archivo** `public/css/monitor-one-variables.css` con todas las variables
3. **Crear archivo** `public/css/monitor-one-components.css` con los componentes
4. **Incluir ambos CSS** en el layout antes de app.css
5. **Actualizar clases** en vistas existentes gradualmente

### 10.2 Prioridad de Migracion

| Prioridad | Componente | Impacto |
|-----------|------------|---------|
| 1 | Sidebar y Navbar | Alto - visible en toda la app |
| 2 | Tablas DataTable | Alto - usado en todas las listas |
| 3 | Cards | Medio - contenedores principales |
| 4 | Botones | Medio - acciones del usuario |
| 5 | Formularios | Bajo - funcionalidad primero |

### 10.3 Archivos a Crear

```
public/
├── css/
│   ├── monitor-one-variables.css    # Variables CSS
│   ├── monitor-one-components.css   # Componentes
│   └── monitor-one-overrides.css    # Overrides Bootstrap
```

---

## 11. INTEGRACION CON NEO4J

```cypher
// Crear nodo de estandares UI
CREATE (std:EstandarUI {
  id: 'UI-MONITOR-ONE-V12',
  nombre: 'Monitor One Design System',
  fuente: 'Azure DevOps - web-monitor',

  // Colores principales
  primary: '#003A81',
  secondary: '#EC7126',
  accent: '#05C1CA',
  cardHeader: '#01214d',

  // Tipografia
  fontFamily: 'Poppins',
  fontWeights: [300, 400, 500, 600, 700],

  // Componentes
  componentes: ['Button', 'Card', 'Table', 'Form', 'Sidebar', 'NavTab'],

  fase: '5.5B',
  fecha: datetime()
});

// Relacionar con documentacion UI existente
MATCH (ui:ComponenteUI {id: 'UI-VIEWS-V12'})
MATCH (std:EstandarUI {id: 'UI-MONITOR-ONE-V12'})
CREATE (ui)-[:APLICA_ESTANDAR]->(std);
```

---

## 12. REFERENCIAS

- **Proyecto Monitor One**: Azure DevOps Organization
- **Repositorio**: web-monitor (13f77312-cf5a-48ff-aed8-4db723e0d68a)
- **Archivo Fuente**: `/src/theme/Colors.ts`
- **Componentes Referencia**: `/src/components/`

---

**Documento generado**: 2025-12-19
**Version**: 1.0
**Fase**: 5.5B - Estandares Monitor One (Anexo)
