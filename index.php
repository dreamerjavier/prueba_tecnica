<?php
require_once "Relacion.php";

use Relacion\Relacion;

$relacion = new Relacion();

$options = getopt("", ["repo::", "local::"]);

$repo = $_GET["repo"] ?? ($options["repo"] ?? null);
$local = $_GET["local"] ?? ($options["local"] ?? null);

if (isset($repo)) {
    $simpleResponse = "";
    $response = $relacion->findReferences($repo);
    $it = new RecursiveIteratorIterator(new RecursiveArrayIterator($response));
    foreach ($it as $v) {
        $simpleResponse .= ($v . ", ");
    }
    if (empty($response)) {
        print("Parece que '$repo' no contiene ningún elemento padre, o no existe el repositorio");
    } else {
        print('El repositorio "' . $repo . '" está incluído de manera directa e indirecta en los siguientes repositorios: ');
        print_r(empty($options) ? $simpleResponse : explode(", ", trim($simpleResponse)));
    }
} else {
    if (isset($local)) {
        if ($local == "true") {
            print('La estructura de los repositorios locales es la siguiente: ');
            if (empty($options)) {
                print_r(json_encode($relacion->createTree(true)));
            } else {
                var_dump($relacion->createTree(true));
            }
        } else {
            print('La estructura de los repositorios es la siguiente: ');
            if (empty($options)) {
                print_r(json_encode($relacion->createTree()));
            } else {
                var_dump($relacion->createTree());
            }
        }
    } else {
        print('La estructura de los repositorios es la siguiente: ');
        if (empty($options)) {
            print_r(json_encode($relacion->createTree()));
        } else {
            var_dump($relacion->createTree());
        }
    }
}
