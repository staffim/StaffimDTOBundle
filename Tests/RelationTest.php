<?php

namespace Staffim\DTOBundle\Tests;

use Staffim\DTOBundle\DTO\UnknownValue;
use Staffim\DTOBundle\Serializer\Exclusion\HiddenFieldsExclusionStrategy;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Staffim\DTOBundle\Serializer\SerializationContext;

use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;
use Staffim\DTOBundle\DTO\Model\DTOInterface;

class RelationTest extends KernelTestCase
{
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

    public function testHideFields()
    {
        $bar = new Foo('Some');

        $bar->model = UnknownValue::create();
        $result = $this->serialize($bar);

        $this->assertArrayNotHasKey('model', $result);
    }

    public function testMultipleSerialize()
    {
        $bar = new Foo('Some');

        $bar->model = UnknownValue::create();
        $this->serialize($bar);
        $this->serialize($bar);
        $this->assertTrue(true);
    }

    private function getSerializer()
    {
        return static::$kernel->getContainer()->get('jms_serializer');
    }

    private function getSerializationContext()
    {
        $serializationContext = new SerializationContext;
        $serializationContext->setSerializeNull(true);
        $serializationContext->addExclusionStrategy(new HiddenFieldsExclusionStrategy());

        return $serializationContext;
    }

    private function serialize($object)
    {
        return json_decode($this->getSerializer()->serialize($object, 'json', $this->getSerializationContext()), true);
    }

    protected function setUp(): void
    {
        $this->bootKernel();
    }
}

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
