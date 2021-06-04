<?php
namespace Globals;

class Globals
{
    protected string $repoDirectory = "repositories";

    function getRepoDir()
    {
        return $this->repoDirectory;
    }
}
