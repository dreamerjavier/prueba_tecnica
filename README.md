# Prueba técnica Ampliffy

Se utilizaron un par de algoritmos recursivos para obtener las dos ramificaciones verticales de un Repositorio dado.

## Casos

Se puede navegar a las siguientes url para ver los casos:

```bash
https://javierguerrero.space/ampliffy/ # Nos dará todo el árbol sin ignorar dependencias no listadas

https://javierguerrero.space/ampliffy/?local=true # Nos dará todo el árbol ignorando dependencias no listadas

https://javierguerrero.space/ampliffy/?repo=ampliffy/lib-2 # Nos da la relación de elementos padre a la que pertenece
```

## Casos (Desde línea de comandos)

Se pueden utilizar a las siguientes líneas para ver los casos:

```bash
php index.php # Nos dará todo el árbol sin ignorar dependencias no listadas

php index.php --local="true" # Nos dará todo el árbol ignorando dependencias no listadas

php index.php --repo="ampliffy/lib-4" # Nos da la relación de elementos padre a la que pertenece
```