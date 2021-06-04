<?php
require_once "Relacion.php";

use Relacion\Relacion;

$relacion = new Relacion();
if (isset($_GET["repo"])) {
    print('El repositorio "' . $_GET["repo"] . '" está incluído de manera directa e indirecta en los siguientes repositorios: <br>');
    print_r(json_encode($relacion->findReferences($_GET["repo"])));
} else {
    if (isset($_GET["local"])) {
        if ($_GET["local"] == "true") {
            print_r(json_encode($relacion->createTree(true)));
        } else {
            print('La estructura de los repositorios es la siguiente: <br>');
            print_r(json_encode($relacion->createTree()));
        }
    } else {
        print('La estructura de los repositorios es la siguiente: <br>');
        print_r(json_encode($relacion->createTree()));
    }
}
