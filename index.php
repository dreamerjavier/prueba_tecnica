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
        $flatElements = [];

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
                        array_push($this->inHouseRepos, $result);

                        // Búsqueda de composers
                        $directoryIteration = new DirectoryIterator($this->getRepoDir() . "/" . $result);
                        if (!empty($directoryIteration)) {
                            foreach ($directoryIteration as $key => $composer_file) {
                                if (pathinfo($composer_file, PATHINFO_EXTENSION) == 'json')
                                    array_push($this->composerList, json_decode(file_get_contents($composer_file->getRealPath()), true));
                            }
                        }
                    }
                }
            }

            if (!empty($this->composerList)) {
                foreach ($this->composerList as $composer) {

                    $flatElements[] = ["name" => $composer["name"], "child" => $this->buildTree($composer)];
                }

                if (!empty($flatElements)) {
                    $arrayResponse = $flatElements;
                }
            }
        }


        return $arrayResponse;
    }

    public function buildTree($composer)
    {

        $branch = array();
        foreach ($composer["require"] as $key => $child) {
            $repoIndex = array_search($key, array_column($this->composerList, 'name'));
            $branch[] = ["name" => $key, "child" => $this->buildTree($this->composerList[$repoIndex])];
        }

        return $branch;
    }
}

$relacion = new Relacion();
print_r(json_encode($relacion->getPackageRelation()));
