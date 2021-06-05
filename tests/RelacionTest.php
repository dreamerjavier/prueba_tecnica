<?php
require_once __DIR__ . "/../Relacion.php";
require_once __DIR__ . "/../Globals.php";

use Relacion\Relacion;
use PHPUnit\Framework\TestCase;

class RelacionTest extends TestCase
{

    protected $Relacion;

    public function setUp(): void
    {
        $this->Relacion = new Relacion();
    }

    public function testCreateTree()
    {
        $respuesta = $this->Relacion->createTree();
        $json = '{"3":{"name":"ampliffy\/proyecto-1","child":[{"name":"ampliffy\/lib-1","child":[]},{"name":"ampliffy\/lib-2","child":[{"name":"ampliffy\/lib-4","child":[]},{"name":"otra\/libreria5","child":[]}]},{"name":"otra\/libreria3","child":[]}]},"4":{"name":"ampliffy\/proyecto-2","child":[{"name":"ampliffy\/lib-2","child":[{"name":"ampliffy\/lib-4","child":[]},{"name":"otra\/libreria5","child":[]}]},{"name":"otra\/libreria6","child":[]}]}}';
        $this->assertEquals($json, json_encode($respuesta));
    }

    public function testCreateTreeWithLocalRepos()
    {
        $respuesta = $this->Relacion->createTree(true);
        $json = '{"3":{"name":"ampliffy\/proyecto-1","child":[{"name":"ampliffy\/lib-1","child":[]},{"name":"ampliffy\/lib-2","child":[{"name":"ampliffy\/lib-4","child":[]}]}]},"4":{"name":"ampliffy\/proyecto-2","child":[{"name":"ampliffy\/lib-2","child":[{"name":"ampliffy\/lib-4","child":[]}]}]}}';
        $this->assertEquals($json, json_encode($respuesta));
    }

    public function testCreateTreeWithGivenDirectory()
    {
        $respuesta = $this->Relacion->createTree(false, "different_dir");
        $this->assertEmpty($respuesta);
    }

    /**
     * @dataProvider repoNameDataProviderWithParents
     */
    public function testFindReferencesWithParents($repoName)
    {
        $respuesta = $this->Relacion->findReferences($repoName);
        $this->assertTrue(!empty($respuesta));
    }

    /**
     * @dataProvider repoNameDataProviderWithNoParents
     */
    public function testFindReferencesWithNoParents($repoName)
    {
        $respuesta = $this->Relacion->findReferences($repoName);
        $this->assertTrue(empty($respuesta));
    }

    public function repoNameDataProviderWithParents()
    {
        return [
            ['ampliffy/lib-1'],
            ['ampliffy/lib-2'],
            ['ampliffy/lib-4'],
        ];
    }

    public function repoNameDataProviderWithNoParents()
    {
        return [
            ['ampliffy/proyecto-1'],
            ['ampliffy/proyecto-2'],
        ];
    }
}
