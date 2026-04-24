Documentación del Proyecto

Integrantes:

Yerovi Josué Martínez Gómez SMSS030924. 
Diego Steven Montoya Castro SMSS054024. 


Preguntas

¿Cómo manejan la conexión a la BD y qué pasa si algunos de los datos son incorrectos? Justifiquen la manera de validación de la conexión.

La conexión a la base de datos se maneja a través del archivo `conexion.php`, el cual utiliza la extensión `mysqli` de PHP para establecer la conexión con el servidor de base de datos MySQL. En caso de que los datos de conexión sean incorrectos (por ejemplo, nombre de usuario, contraseña o nombre de la base de datos), se utiliza la función `die()` para detener la ejecución del script y mostrar un mensaje de error. Esto asegura que no se realicen operaciones adicionales en caso de que la conexión no sea exitosa, evitando posibles errores o inconsistencias en la aplicación.

¿Cuál es la diferencia entre $_GET y $_POST en PHP? ¿Cuándo es más apropiado usar cada uno? Da un ejemplo real de tu proyecto.

$_GET: Envía los datos a través de la URL como parámetros visibles. Es más apropiado para solicitudes donde no se maneja información sensible y se desea que los datos sean visibles, como en la paginación o en búsquedas.
  Ejemplo en el proyecto: En la vista pública (vista_publica.php), se utiliza $_GET para obtener el ID de un aspirante y mostrar su información detallada.

$_POST: Envía los datos de manera oculta en el cuerpo de la solicitud HTTP. Es más apropiado para formularios que manejan información sensible o que envían grandes cantidades de datos.
  Ejemplo en el proyecto: En el archivo admin.php, se utiliza $_POST para enviar los datos de los formularios de creación y edición de aspirantes, ya que estos contienen información sensible como nombres y direcciones.

Tu app va a usarse en una empresa de la zona oriental. ¿Qué riesgos de seguridad identificas en una app web con BD que maneja datos de los usuarios? ¿Cómo los mitigarían?

1. Inyección SQL: Un atacante podría intentar inyectar código SQL malicioso para acceder o manipular la base de datos.
   Mitigación: Uso de consultas preparadas y declaraciones parametrizadas con `mysqli` para evitar la ejecución de código malicioso.

2. Falta de validación de datos: Datos no validados podrían permitir la ejecución de scripts maliciosos o causar errores en la aplicación.
   Mitigación: Validar y sanitizar todos los datos de entrada antes de procesarlos o almacenarlos en la base de datos.

3. Exposición de datos sensibles: Información sensible de los usuarios podría ser expuesta si no se protege adecuadamente.
   Mitigación: Implementar HTTPS para cifrar las comunicaciones y restringir el acceso a la base de datos mediante permisos adecuados.

4. Ataques de fuerza bruta: Un atacante podría intentar adivinar credenciales de acceso.
   Mitigación: Implementar límites en los intentos de inicio de sesión y utilizar contraseñas seguras.

5. Gestión de sesiones: Las sesiones podrían ser secuestradas si no se manejan correctamente.
   Mitigación: Usar cookies seguras, establecer tiempos de expiración y regenerar IDs de sesión después de cada inicio de sesión.

Diccionario de Datos

Tabla: aspirantes

| Columna        | Tipo de dato | Límite de caracteres | ¿Es nulo? | Descripción                          |
|----------------|--------------|-----------------------|-----------|--------------------------------------|
| id             | INT          | N/A                  | No        | Identificador único del aspirante.  |
| nombre         | VARCHAR      | 100                  | No        | Nombre completo del aspirante.      |
| apellido       | VARCHAR      | 100                  | No        | Apellido del aspirante.             |
| correo         | VARCHAR      | 150                  | No        | Correo electrónico del aspirante.   |
| telefono       | VARCHAR      | 15                   | No        | Número de teléfono del aspirante.   |
| direccion      | TEXT         | N/A                  | No        | Dirección del aspirante.            |
| carrera        | VARCHAR      | 100                  | No        | Carrera a la que aplica el aspirante.|
| fecha_registro | DATE         | N/A                  | No        | Fecha de registro del aspirante.    |

Tabla: usuarios

| Columna        | Tipo de dato | Límite de caracteres | ¿Es nulo? | Descripción                          |
|----------------|--------------|-----------------------|-----------|--------------------------------------|
| id             | INT          | N/A                  | No        | Identificador único del usuario.    |
| username       | VARCHAR      | 50                   | No        | Nombre de usuario.                  |
| password       | VARCHAR      | 255                  | No        | Contraseña del usuario.             |
| rol            | ENUM         | N/A                  | No        | Rol del usuario (admin o usuario).  |