# API DocDoc
Rutas de la API de DocDoc. La URL de la API es https://io.docdoc.com.ar

# Índice
* [Login](#login)
* [Empresa](#parametros-de-empresa)
 * [Dame Parámetro](#dame-parámetro) 
* [Casos](#casos)
 * [Alta de Casos](#alta-de-casos)
 * [Dame](#dame-caso)
 * [Previsualizar Borrado](#previsualizar-borrado-de-caso)
 * [Borrar Caso](#borrar-caso)
 * [Modificar Caso](#modificar-caso)
 * [Archivar Caso](#archivar-caso)
 * [Dar de Baja Caso](#dar-de-baja-caso)
 * [Dame Persona de Caso](#dame-persona-de-caso)
* [Movimientos](#movimientos)
 * [Alta de Movimiento](#alta-de-movimiento)
 * [Modificar Movimiento](#modificar-movimiento) 
 * [Borrar Movimiento](#borrar-movimiento) 
 * [Realizar Movimiento](#realizar-movimiento)  
 * [Desrealizar Movimiento](#desrealizar-movimiento) 
 * [Dame Movimiento](#dame-movimiento)
 * [Asociar Objetivo](#asociar-objetivo)
 * [Desasociar Objetivo](#desasociar-objetivo)
 * [Vista Tribunales](#vista-tribunales)
* [Consultas](#consultas)
 * [Listar Consultas de Estudio](#listar-consultas-de-estudio) 
 * [Dame Consulta](#dame-consulta)
 * [Aceptar Derivación](#aceptar-derivación)
 * [Rechazar Derivación](#rechazar-derivación)
* [Estados de Caso](#estados-de-caso)
 * [Dame EstadoCaso](#dame-estadocaso)
* [Estudios](#estudios) 
 * [Listar Estados de Caso](#listar-estados-de-caso) 
 * [Listar Orígenes de Caso](#listar-orígenes-de-caso)
 * [Listar Tipos de Movimiento](#listar-tipos-de-movimiento)
 * [Dame Intervalo de Fechas de Movimientos](#dame-intervalo-de-fechas-de-movimientos)
 * [Listar Usuarios](#listar-usuarios-estudio)
 * [Modificar Persona](#modificar-persona)
* [Jurisdicciones](#jurisdicciones) 
 * [Buscar Jurisdicciones](#buscar-jurisdicciones)
 * [Dame Jurisdicción](#dame-jurisdicción)
* [Juzgados](#juzgados)
 * [Buscar Juzgados](#buscar-juzgados) 
 * [Dame Juzgado](#dame-juzgado)
* [Nominaciones](#nominaciones)
 * [Buscar Nominaciones](#buscar-nominaciones)
 * [Dame Nominación](#dame-nominación)
* [Origenes](#origenes)
 * [Dame Origen](#dame-origen)  
* [Roles de Tipo de Caso](#roles-de-tipo-de-caso)
 * [Dame Rol de Tipo de Caso](#dame-rol-de-tipo-de-caso)   
* [Tipos de Caso](#tipos-de-caso)
 * [Buscar Tipos de Caso](#buscar-tipos-de-caso) 
 * [Dame Tipo de Caso](#dame-tipo-de-caso)
 * [Listar Roles de Tipo de Caso](#listar-roles-de-tipo-de-caso) 
* [Tipos de Movimiento](#tipos-de-movimiento)
 * [Dame Tipo de Movimiento](#dame-tipo-de-movimiento) 
* [Usuarios](#usuarios)
 * [Dame Usuario](#dame-usuario) 
 * [Cambiar Password](#cambiar-password)
 * [Validar Código](#validar-código)
 * [Listar Permisos](#listarpermisos) 
* [Objetivos](#objetivos)
 * [Alta Objetivo](#alta-objetivo)
 * [Modificar Objetivo](#modificar-objetivo)
 * [Borrar Objetivo](#borrar-objetivo) 
 * [Dame Objetivo](#dame-objetivo)
 * [Listar Objetivos de un Caso](#listar-objetivos-de-un-caso)
* [Personas](#personas)
 * [Alta Teléfono](#alta-teléfono) 
 
## Login

### Login

`POST /api/login`
* Parámetros

```json
{
    "Usuario": string,
    "Password" : string
}
```

***

## Casos

### Alta de Casos

`POST /api/casos`
* Parámetros

```json
{
    "IdJuzgado" : "integer",
    "IdNominacion" : "integer"(opcional),
    "IdTipoCaso" : "integer",
    "IdEstadoCaso" : "integer",
    "IdOrigen" : "integer" (opcional),
    "Caratula" : "string",
    "NroExpediente" : "string" (opcional),
    "Carpeta" : "string" (opcional),
    "Observaciones" : "string" (opcional),
    "Personas" : [ -- opcional
        {
            "Nombres" : "string",
            "Apellidos" : "string" (opcional cuando el tipo es J),
            "Domicilio" : "string" (opcional),
            "Email" : "string" (opcional),
            "TipoPersona" : ["J" | "F"], -- J: Jurídica - F: Física
            "EsPrincipal" : ["S" | "N"],
            "IdRTC" : "integer",
            "Documento" : "string",
            "Cuit" : "string" (opcional cuando el tipo es F),
            "Observaciones" : "string",
            "Telefonos" : ["tel1", "tel2",...,"telN"] (opcional)
        }
    ]
}

```

***

### Dame Caso

`GET /api/casos/:id`

***

### Previsualizar Borrado de Caso

`GET /api/casos/:id/previsualizar-borrado`

***

### Borrar Caso

`DELETE /api/casos/:id`

***

### Modificar Caso

`PUT /api/casos/:id`

* Parámetros

```json
{
    "IdJuzgado" : integer,
    "IdNominacion" : integer (opcional),
    "IdEstadoCaso" : integer,
    "Caratula" : string,
    "NroExpediente" : string (opcional),
    "Carpeta" : string (opcional),
    "Observaciones" : string (opcional)
}

```

***

### Archivar Caso

`PUT /api/casos/:id/archivar`

***

### Dar de Baja Caso

`PUT /api/casos/:id/baja`

***

### Dame Persona De Caso

`GET /api/casos/:idCaso/persona/:idPersona`

***


## Parámetros de Empresa

### Dame Parámetro

`GET /api/empresa?Parametro=param`

* Parámetros:
 * Parametro: string

***

## Estados de Caso

### Dame EstadoCaso

`GET /api/estados-caso/:id`

***

## Estudios

### Dame Estudio

`GET /api/estudios/:id`

***

### Listar Estados de Caso

`GET /api/estudios/:id/estados-caso`

***

### Listar Orígenes de Caso

`GET /api/estudios/:id/origenes`

***

### Listar Tipos de Movimiento

`GET /api/estudios/:id/tipos-movimiento`

***

### Dame Intervalo de Fechas de Movimientos

`GET /api/estudios/:id/intervalo-fechas-movimientos`

***

### Listar Usuarios

`GET /api/estudios/:id/usuarios`

***

### Modificar Persona

`PUT /api/estudios/:id/modificar-persona/:idPersona`

```json
{
    "Nombres" : "string",
    "Apellidos" : "string" (opcional cuando el tipo es J),
    "Domicilio" : "string" (opcional),
    "Email" : "string" (opcional),
    "Tipo" : ["J" | "F"], -- J: Jurídica - F: Física
    "Documento" : "string",
    "Cuit" : "string" (opcional cuando el tipo es F),
}
```

***

## Jurisdicciones

### Buscar Jurisdicciones 

`GET /api/jurisdicciones?Cadena=abc&IncluyeBajas=N`

* Parámetros:
 * Cadena: string
 * IncluyeBajas : [S|N]

*** 

### Dame Jurisdicción

`GET /api/jurisdicciones/:id`

***

## Juzgados

### Buscar Juzgados

`GET /api/juzgados?IdJurisdiccion=6&Cadena=abc&IncluyeBajas=N`

* Parámetros:
 * IdJurisdiccion: integer 
 * Cadena: string
 * IncluyeBajas : [S|N]

*** 

### Dame Juzgado

`GET /api/juzgados/:id`

***

## Nominaciones

### Buscar Nominaciones

`GET /api/nominaciones?IdJuzgado=15&Cadena=abc&IncluyeBajas=N`

* Parámetros:
 * IdJuzgado: integer 
 * Cadena: string
 * IncluyeBajas : [S|N]

*** 

### Dame Nominación

`GET /api/nominaciones/:id`

***

## Origenes

### Dame Origen

`GET /api/origenes/:id`

***

## Roles de Tipo de Caso

### Dame Rol de Tipo de Caso

`GET /api/roles-tipo-caso/:id`

***

## Tipos de Caso

### Buscar Tipos de Caso

`GET /api/tipos-caso?Cadena=abc&IncluyeBajas=N`

* Parámetros:
 * Cadena: string
 * IncluyeBajas : [S|N]

*** 

### Dame Tipo de Caso

`GET /api/tipos-caso/:id`

***

### Listar Roles de Tipo de Caso

`GET /api/tipos-caso/:id/roles`

***

## Tipos de Movimiento

### Dame Tipo de Movimiento

`GET /api/tipos-movimiento/:id`

***

## Usuarios

### Usuario actual

Retorna datos del usuario logueado. Ejemplo respuesta: 

```
{  
   "IdEstudio":"3",
   "IdUsuario":"25",
   "IdUsuarioPadre":null,
   "IdEstudioPadre":"3",
   "IdRolEstudio":"5",
   "Estado":"A",
   "IdConsulta":null,
   "RolEstudio":"Abogado",
   "Estudio":"Estudio de Desarrolladores",
   "IdRol":null,
   "Nombres":"Miguel",
   "Apellidos":"Liezun",
   "Usuario":"mliezun",
   "Password":null,
   "Token":"DL6ojOazjRDCxfXyruI8AIIjp3muUBoWddfxMFu4Xm_cAWtG43C0dy9qZmuTfEuePQ0wNors9oMAzVNOPYRwzzBqESbCn3va8RG75XzALlWx_Jor",
   "Email":"lxx@gmail.com",
   "IntentosPass":null,
   "FechaUltIntento":null,
   "FechaAlta":null,
   "DebeCambiarPass":"N",
   "Observaciones":""
}
```

`GET /api/usuarios`

***

### Dame Usuario

`GET /api/usuarios/:id`

***

### Cambiar Password

`POST /api/usuarios/cambiar-password`

* Parámetros

```json
{
    "OldPass": string,
    "NewPass": string,
    "Token" : string
}
```

***

### Validar Código

`POST /api/usuarios/validar-codigo`

* Parámetros:

```json
{
    "Codigo" : string,
    "Usuario" : string
}
```

***

### Listar Permisos

`GET /api/usuarios/:id/listar-permisos`

***

## Objetivos

### Alta Objetivo

`POST /api/objetivos`
* Parámetros:


```json
{
    "IdCaso" : integer,
    "Objetivo": string
}
```

***

### Modificar Objetivo

`PUT /api/objetivos/:id`

* Parámetros:

```json
{
    "Objetivo": string
}
```

***

### Borrar Objetivo

`DELETE /api/objetivos/:id`

***

### Dame Objetivo

`GET /api/objetivos/:id`

***

### Listar Objetivos de un Caso

`GET /api/objetivos?IdCaso=434`


## Movimientos

### Alta de Movimiento

`POST /api/movimientos`

* Parámetros:

```json
{
    "IdCaso" : integer,
    "IdTipoMov": integer,
    "IdResponsable": integer,
    "Detalle": string,
    "FechaEsperada": date,
    "Cuaderno": string,
    "Escrito": string,
    "Color": string,
    "FechaAlta": date,
    "Multimedia": [
        {
            "Url": string,
            "Tipo": ['D': Documento - 'V': Video - 'A': Audio - 'I': Imagen]
        },...
    ]
    
}
```
***

### Modificar Movimiento

`PUT /api/movimientos/:idMovimientoCaso`

* Parámetros:

```json
{
    "IdTipoMov": integer,
    "IdResponsable": integer,
    "Detalle": string,
    "FechaEsperada": date,
    "Cuaderno": string,
    "Escrito": string,
    "Color": string
}
```

***

### Borrar Movimiento

`DELETE /api/movimientos/:idMovimientoCaso`

***

### Realizar Movimiento

`PUT /api/movimientos/:idMovimientoCaso/realizar`

***

### Desrealizar Movimiento

`PUT /api/movimientos/:idMovimientoCaso/desrealizar`

***

### Dame Movimiento

`GET /api/movimientos/:idMovimientoCaso`

***

### Asociar Objetivo

`POST /api/movimientos/:id_movimiento_caso/asociar-objetivo/:id_objetivo`

***

### Desasociar Objetivo

`PUT /api/movimientos/:id_movimiento_caso/desasociar-objetivo/:id_objetivo`

***

### Vista Tribunales

`GET /api/movimientos/vista-tribunales`

***

## Consultas

### Listar Consultas de Estudio

`GET /api/consultas`

***

### Dame Consulta

`GET /api/consultas/:id`

***

### Aceptar Derivación

`PUT /api/consultas/:id_derivacion_consulta/aceptar-derivacion`

***

### Rechazar Derivación

`PUT /api/consultas/:id_derivacion_consulta/rechazar-derivacion`

***

## Personas

### Alta Teléfono

`POST /api/personas/:id_persona/alta-telefono`

* Parámetros:

```json
{
    "Telefono": "3815455455"
}
```

