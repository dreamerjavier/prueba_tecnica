<?php
class Globals
{
    protected string $repoDirectory = "repositories";

    function getRepoDir()
    {
        return $this->repoDirectory;
    }
}

class Relacion extends Globals
{
    public function getPackageRelation($packageName = null)
    {
        /* if (is_null($packageName))
            return ["code" => "error", "message" => "El nombre del paquete está vacío"]; */
        $treeArray = $this->createTree();
        return $treeArray;
    }

    public function createTree(string $directory = null)
    {
        $arrayResponse = [];
        $composerList = [];
        $flatElements = [];

        // Lectura de repositorios
        $globals = new Globals;
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
                                if (pathinfo($composer_file, PATHINFO_EXTENSION) == 'json')
                                    array_push($composerList, json_decode(file_get_contents($composer_file->getRealPath()), true));
                            }
                        }
                    }
                }
            }

            if (!empty($composerList)) {
                foreach ($composerList as $composer) {
                    $flatElements[] = ["name" => $composer["name"], "child" => array_keys($composer["require"])];
                }
            }

            foreach ($flatElements as $key => $flatElement) {
                foreach ($flatElements as $toCompareFlat) {
                    if (in_array($flatElement["name"], $toCompareFlat["child"]))
                        $flatElements[$key]["parents"][] = $toCompareFlat["name"];
                }
            }

            if (!empty($flatElements)) {
                $arrayResponse = $flatElements;
            }
        }


        return $arrayResponse;
    }
}

$relacion = new Relacion();
print_r($relacion->getPackageRelation());
