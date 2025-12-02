# 🏥 Proyecto Web para Gestión Hospitalaria

## 1. Presentación y Objetivos
Este proyecto consiste en el desarrollo de una aplicación web para la gestión de pacientes, médicos, citas y consultas dentro de un hospital. Es parte de la asignatura **Implantación de Aplicaciones Web** y está orientado a mejorar la organización interna mediante una interfaz eficiente, un sistema de autenticación por roles y una base de datos bien estructurada.

### Objetivo General
El propósito principal es el desarrollo de una aplicación web dedicada a la administración y gestión integral de un hospital, digitalizando el flujo de trabajo médico desde la gestión de citas hasta la emisión de recetas.

### Objetivos Específicos
* **Interacción Centralizada:** Facilitar la interacción entre médicos y pacientes mediante una plataforma centralizada.
* **Historial Médico:** Mantener un registro histórico de consultas, diagnósticos y tratamientos.
* **Seguridad:** Gestionar de forma segura la autenticación y los roles de usuario dentro de la organización.

---

## 2. Tecnologías Utilizadas
El desarrollo del proyecto se basa en el siguiente stack tecnológico:
* **Frontend:** HTML, CSS, JavaScript.
* **Backend:** PHP (con manejo de sesiones y roles).
* **Base de datos:** MySQL/MariaDB.

---

## 3. Usuarios y Roles
El sistema está diseñado para ser manejado por diferentes actores, definidos en la estructura de la base de datos mediante la tabla `usuario` y su campo `rol`.

* **Administrador:** Encargado de la gestión global (altas de médicos, gestión de especialidades) y acceso al panel de control completo.
* **Médico:** Utiliza el sistema para ver su agenda, gestionar citas, realizar consultas y emitir recetas.
* **Paciente:** Accede para solicitar citas, ver su historial y consultar sus recetas.

---

## 4. Modelo de Datos y Base de Datos
El núcleo del sistema se basa en una base de datos relacional robusta.

### Diagrama Entidad-Relación (Mermaid)
Este diagrama refleja la estructura implementada en SQL.

```mermaid
erDiagram
    PACIENTE {
        INT id_paciente PK
        VARCHAR nombre
        VARCHAR apellidos
        DATE fecha_nacimiento
        VARCHAR direccion
        VARCHAR telefono
        VARCHAR email
        TEXT afecciones
        DATETIME fecha_registro
    }

    MEDICO {
        INT id_medico PK
        VARCHAR nombre
        VARCHAR apellidos
        INT especialidad_id FK
        VARCHAR email
        VARCHAR telefono
    }

    USUARIO {
        INT id_usuario PK
        VARCHAR username
        VARCHAR password_hash
        ENUM rol
        INT medico_id FK
    }

    ESPECIALIDAD {
        INT id_especialidad PK
        VARCHAR nombre
    }

    CITA {
        INT id_cita PK
        INT paciente_id FK
        INT medico_id FK
        INT especialidad_id FK
        DATE fecha
        TIME hora
        TEXT motivo
    }

    CONSULTAS {
        INT id_consulta PK
        INT id_cita FK
        TEXT diagnostico
        TEXT observaciones
    }

    RECETAS {
        INT id_receta PK
        INT consulta_id FK
        VARCHAR medicamento
        VARCHAR dosis
        TEXT instrucciones
    }

    PACIENTE ||--o{ CITA : "tiene"
    MEDICO ||--o{ CITA : "atiende"
    ESPECIALIDAD ||--o{ MEDICO : "es de"
    ESPECIALIDAD ||--o{ CITA : "para"
    MEDICO ||--o{ USUARIO : "puede tener"
    CITA ||--|| CONSULTAS : "puede generar"
    CONSULTAS ||--o{ RECETAS : "puede tener"