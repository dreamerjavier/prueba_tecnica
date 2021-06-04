<?php
namespace Relacion;

require_once "Globals.php";

use DirectoryIterator;
use Globals\Globals as Globals;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class Relacion extends Globals
{
    public $inHouseRepos = [];
    public $composerList = [];
    public $dimensionalElements = [];
    public $flatElements = [];
    public bool $onlyLocalRepos;

    function createTree($local = false, string $directory = null)
    {
        $this->onlyLocalRepos = $local;

        $arrayResponse = [];

        // Lectura de repositorios
        $directory = is_null($directory) ? $this->getRepoDir() : $directory;
        if (is_dir($this->getRepoDir())) {
            $scanResult = scandir($this->getRepoDir());
            if (!empty($scanResult)) {
                foreach ($scanResult as $result) {
                    if (!pathinfo($result, PATHINFO_EXTENSION)) {

                        // Búsqueda de composers
                        $directoryIteration = new DirectoryIterator($this->getRepoDir() . "/" . $result);
                        if (!empty($directoryIteration)) {
                            foreach ($directoryIteration as $key => $composer_file) {
                                if (pathinfo($composer_file, PATHINFO_EXTENSION) == 'json') {
                                    $json = json_decode(file_get_contents($composer_file->getRealPath()), true);
                                    array_push($this->composerList, $json);
                                    array_push($this->inHouseRepos, $json["name"]);
                                }
                            }
                        }
                    }
                }
            }

            //Evaluamos si ha conseguido los composers
            if (!empty($this->composerList)) {
                foreach ($this->composerList as $composer) {
                    // Utilizamos la lista flat para tener todos los elementos en una lista lineal para poder comparar más tarde
                    $this->flatElements[] = ["name" => $composer["name"], "child" => array_keys($composer["require"])];

                    //Aquí se crea el árbol de dependencias
                    $this->dimensionalElements[] = ["name" => $composer["name"], "child" => $this->createBranch($composer)];
                }


                foreach ($this->flatElements as $key => $flatElement) {
                    foreach ($this->flatElements as $toCompareFlat) {

                        //Evaluamos si tiene padres
                        if (in_array($flatElement["name"], $toCompareFlat["child"])) {
                            if (!in_array($toCompareFlat["name"], $this->flatElements[$key]["parents"]) and $toCompareFlat["name"] != $flatElement["name"]) {
                                $this->flatElements[$key]["parents"][] = $toCompareFlat["name"];
                            }
                        }

                        $parentKey = array_search($toCompareFlat["name"], array_column($this->flatElements, 'name'));
                        if (!in_array($toCompareFlat["name"], $this->flatElements[$parentKey]["child"]) and $toCompareFlat["name"] != $this->flatElements[$parentKey]["name"]) {
                            $this->flatElements[$parentKey]["child"][] = $toCompareFlat["name"];
                        }
                    }
                }

                foreach ($this->flatElements as $element) {
                    //Creamos una lista con los elementos que no son padres principales
                    if (isset($element["parents"])) {
                        $nonMainParentItems[] = $element["name"];
                    }
                }


                foreach ($this->dimensionalElements as $key => $element) {

                    //Limpiamos la lista del árbol principal
                    if (in_array($element["name"], $nonMainParentItems)) {
                        unset($this->dimensionalElements[$key]);
                    }
                }

                if (!empty($this->dimensionalElements)) {
                    $arrayResponse = $this->dimensionalElements;
                }
            }
        }


        return $arrayResponse;
    }

    function createBranch($composer)
    {

        $branch = array();
        foreach ($composer["require"] as $key => $child) {
            $repoIndex = array_search($key, array_column($this->dimensionalElements, 'name'));

            //Evaluamos si es parte de los repositorios locales
            if ($this->onlyLocalRepos) {
                if (in_array($key, $this->inHouseRepos)) {
                    $branch[] = ["name" => $key, "child" => $this->createBranch($this->composerList[$repoIndex])];
                }
            } else {
                $branch[] = ["name" => $key, "child" => $this->createBranch($this->composerList[$repoIndex])];
            }
        }

        return $branch;
    }

    public function findReferences($packageName)
    {
        $treeArray = $this->createTree();
        $response = "";

        if (!empty($treeArray)) {

            //Buscamos en la lista de elementos planos
            $search = $this->search_by_package_name($packageName, $this->flatElements);
            if (!is_null($search)) {
                if (isset($search["parents"]) and !empty($search["parents"])) {
                    //Comienza la recursividad de padres
                    $linkedRepos = $this->getPackageParents($search["parents"]);
                } else {
                    $linkedRepos = ["No contiene ningún elemento padre"]; //No tiene elementos padre
                }
            }
        }

        $it = new RecursiveIteratorIterator(new RecursiveArrayIterator($linkedRepos));
        foreach ($it as $v) {
            $response .= ($v . ", ");
        }
        return $response;
    }

    function search_by_package_name($name, $array)
    {
        foreach ($array as $composerElement) {
            foreach ($composerElement as $key => $composer_name) {
                if ($composer_name == $name) {
                    return $composerElement;
                }
            }
        }
        return null;
    }

    function getPackageParents($parents)
    {
        $linkedRepos = $parents;
        foreach ($parents as $element) {
            $search = $this->search_by_package_name($element, $this->flatElements);
            if (!is_null($search)) {
                if (isset($search["parents"]) and !empty($search["parents"])) {
                    $linkedRepos[] = $this->getPackageParents($search["parents"]);
                }
            }
        }

        return $linkedRepos;
    }
}
