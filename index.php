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
    public $inHouseRepos = [];
    public $composerList = [];
    public $dimensionalElements = [];
    public $flatElements = [];

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

        $descendantTree = [];
        $ascendantTree = [];

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

            if (!empty($this->composerList)) {
                foreach ($this->composerList as $composer) {
                    $this->flatElements[] = ["name" => $composer["name"], "child" => array_keys($composer["require"])];
                    $this->dimensionalElements[] = ["name" => $composer["name"], "child" => $this->buildTree($composer)];
                }

                foreach ($this->flatElements as $key => $flatElement) {
                    foreach ($this->flatElements as $toCompareFlat) {
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
                    if (isset($element["parents"])) {
                        $nonMainParentItems[] = $element["name"];
                    }
                }

                foreach ($this->dimensionalElements as $key => $element) {
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

    public function buildTree($composer)
    {

        $branch = array();
        foreach ($composer["require"] as $key => $child) {
            $repoIndex = array_search($key, array_column($this->dimensionalElements, 'name'));
            if (in_array($key, $this->inHouseRepos)) {
                $branch[] = ["name" => $key, "child" => $this->buildTree($this->composerList[$repoIndex])];
            }
        }

        return $branch;
    }

    public function findReferences()
    {
    }
}

$relacion = new Relacion();
print_r(json_encode($relacion->getPackageRelation()));
