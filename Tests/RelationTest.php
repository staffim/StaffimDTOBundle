<?php

namespace Staffim\DTOBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Staffim\DTOBundle\Serializer\SerializationContext;

use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;
use Staffim\DTOBundle\DTO\Model\DTOInterface;

class RelationTest extends KernelTestCase
{
    private $serializationContext;

    public function testSingleRelation()
    {
        $baz = new Foo('baz');
        $foo = new Foo('foo');
        $foo->model = $baz;
        $result = $this->serialize($foo);

        $this->assertEquals('string', gettype($result['model']));
        $this->assertEquals('baz', $result['model']);

        $this->assertArrayHasKey('model', $result['_embedded']);
        $this->assertEquals('baz', $result['_embedded']['model']['id']);
    }

    public function testCollectionRelation()
    {
        $baz = new Foo('baz');
        $foo = new Foo('foo');
        $foo->models = [$baz];
        $result = $this->serialize($foo);

        $this->assertEquals(['baz'], $result['models']);

        $this->assertArrayHasKey('models', $result['_embedded']);
        $this->assertEquals('baz', $result['_embedded']['models'][0]['id']);
    }

    public function testEmbedRelation()
    {
        $bar = new Foo('bar');
        $baz = new Foo('baz');
        $baz->model = $bar;
        $foo = new Foo('foo');
        $foo->model = $baz;

        $result = $this->serialize($foo);

        $this->assertEquals('baz', $result['model']);

        $this->assertEquals('bar', $result['_embedded']['model']['model']);
        $this->assertArrayHasKey('model', $result['_embedded']['model']['_embedded']);
        $this->assertEquals('bar', $result['_embedded']['model']['_embedded']['model']['id']);
    }

    private function getSerializer()
    {
        return static::$kernel->getContainer()->get('serializer');
    }

    private function getSerializationContext()
    {
        if (!$this->serializationContext) {
            $this->serializationContext = new SerializationContext;
            $this->serializationContext->setSerializeNull(true);
        }

        return $this->serializationContext;
    }

    private function serialize($object)
    {
        return json_decode($this->getSerializer()->serialize($object, 'json', $this->getSerializationContext()), true);
    }

    protected function setUp()
    {
        $this->bootKernel();
    }
}

/**
 * @Hateoas\RelationProvider("staffim_dto.hateoas.dto_relation_provider:addRelations")
 */
class Foo implements DTOInterface
{
    public function __construct($id)
    {
        $this->id = $id;
    }

    public $id;

    /**
     * @Serializer\Type("DTO")
     *
     * @var string
     */
    public $model;

    /**
     * @Serializer\Type("array<DTO>")
     *
     * @var array
     */
    public $models;
}
