Moodle WebService Get Roles (local_ws_subir_imagen_curso)
====================================================

Este complemento local le permite obtener roles de moodles a través de la API REST. No hay posibilidad de obtener identificadores de roles directamente a través de la API de descanso,
aunque hay dos funciones principales que exigen la identificación del rol: core_role_assign_roles y core_role_unassign_roles.

La función local_ws_subir_imagen_curso se agrega al resto de la API. Puede proporcionar listas de ID de roles, nombres de roles y/o nombres breves de roles y obtener la información de los roles asociados (id, nombre, nombre abreviado, descripción, orden de clasificación, arquetipo). Si la identificación, el nombre o el nombre abreviado dados no tienen el rol correspondiente, la información del rol será "nula" a pesar de la entrada de búsqueda.
Si entrega listas vacías, se devolverán todos los roles.

Algunos ejemplos de llamadas a la función de servicio web:

* Get all roles:
https://yoursite.com/webservice/rest/server.php?wstoken=yOurT0k3n&moodlewsrestformat=json&wsfunction=local_ws_subir_imagen_curso

* Get roles with id=1, id=2 or shortname=editingteacher:
  https://yoursite.com/webservice/rest/server.php?wstoken=yOurT0k3n&moodlewsrestformat=json&wsfunction=local_ws_subir_imagen_curso&ids[0]=1&ids[1]=2&shortnames[0]=editingteacher

* Get role with name=Teacher:
  https://yoursite.com/webservice/rest/server.php?wstoken=yOurT0k3n&moodlewsrestformat=json&wsfunction=local_ws_subir_imagen_curso&names[0]=Teacher 


Configuración
-------------
No se necesita configuración, solo instale el complemento. Tenga en cuenta agregar las funciones al rest-service.

Uso
-----
Utilice funciones sobre la API Rest.

Requisitos
------------
- Moodle 3.3 o posterior

Instalación
------------
Copie la carpeta local_ws_subir_imagen_curso en su directorio /local. Añade las funciones a tu servicio de descanso.

Autor
------
Corvus Albus