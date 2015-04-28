<?php

namespace Kampaw\Dic\Definition;

/**
 * @coversDefaultClass \Kampaw\Dic\Definition\DefinitionFactory
 * @covers ::<!public>
 */
class DefinitionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DefinitionFactory $factory
     */
    private $factory;

    public function setUp()
    {
        $this->factory = new DefinitionFactory();
    }

    /**
     * @test
     * @covers ::getDefinition
     */
    public function GetDefinition_ClassExists_ReturnsDefinition()
    {
        $result = $this->factory->getDefinition('\stdClass');

        $this->assertSame('\stdClass', $result['concrete']);
    }

    /**
     * @test
     * @covers ::getDefinition
     */
    public function GetDefinition_NoConstructor_NoParameters()
    {
        $result = $this->factory->getDefinition('\stdClass');

        $this->assertEmpty($result['parameters']);
    }

    /**
     * @test
     * @covers ::getDefinition
     */
    public function GetDefinition_SingleConstructorParameter_CorrectParameterCount()
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ConcreteParameter';
        $result = $this->factory->getDefinition($asset);

        $this->assertCount(1, $result['parameters']);
    }

    /**
     * @test
     * @covers ::getDefinition
     */
    public function GetDefinition_MultipleConstructorParameters_CorrectParameterCount()
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ConcreteAndScalarParameter';
        $result = $this->factory->getDefinition($asset);

        $this->assertCount(2, $result['parameters']);
    }

    /**
     * @test
     * @covers ::getDefinition
     */
    public function GetDefinition_SingleConstructorParameter_CorrectParameterName()
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ConcreteParameter';
        $result = $this->factory->getDefinition($asset);

        $this->assertSame('concrete', $result['parameters'][0]['name']);
    }

    /**
     * @test
     * @covers ::getDefinition
     */
    public function GetDefinition_SingleConstructorParameter_CorrectParameterType()
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ConcreteParameter';
        $result = $this->factory->getDefinition($asset);

        $this->assertSame('\stdClass', $result['parameters'][0]['type']);
    }

    /**
     * @test
     * @covers ::getDefinition
     */
    public function GetDefinition_ConcreteAndScalarConstructorParameter_CorrectParameterTypes()
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ConcreteAndScalarParameter';
        $result = $this->factory->getDefinition($asset);

        $this->assertSame('\stdClass', $result['parameters'][0]['type']);
        $this->assertArrayNotHasKey('type', $result['parameters'][1]);
    }

    /**
     * @test
     * @covers ::getDefinition
     */
    public function GetDefinition_ConstructorParameterWithDefaultValue_CorrectParameterValue()
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ParameterWithDefaultValue';
        $result = $this->factory->getDefinition($asset);

        $this->assertSame('default', $result['parameters'][0]['value']);
    }

    /**
     * @test
     * @covers ::getDefinition
     */
    public function GetDefinition_StdClassAsSubject_EmptyMutators()
    {
        $result = $this->factory->getDefinition('\stdClass');

        $this->assertCount(0, $result['mutators']);
    }

    /**
     * @test
     * @covers ::getDefinition
     */
    public function GetDefinition_SingleMutatorNoParameters_ReturnsEmptyArray()
    {
        $asset = '\Kampaw\Dic\Assets\MutatorInjection\SingleMutatorNoParameters';
        $result = $this->factory->getDefinition($asset);

        $this->assertCount(0, $result['mutators']);
    }

    /**
     * @test
     * @covers ::getDefinition
     */
    public function GetDefinition_SingleMutatorScalarParameter_ReturnsCorrectName()
    {
        $asset = '\Kampaw\Dic\Assets\MutatorInjection\SingleMutatorScalarParameter';
        $result = $this->factory->getDefinition($asset);

        $this->assertSame('scalar', $result['mutators'][0]['name']);
    }

    /**
     * @test
     * @covers ::getDefinition
     */
    public function GetDefinition_SingleMutatorConcreteParameter_ReturnsCorrectName()
    {
        $asset = '\Kampaw\Dic\Assets\MutatorInjection\SingleMutatorConcreteParameter';
        $result = $this->factory->getDefinition($asset);

        $this->assertSame('concrete', $result['mutators'][0]['name']);
    }
}