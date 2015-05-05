<?php

namespace Kampaw\Dic\Definition;

/**
 * @coversDefaultClass \Kampaw\Dic\Definition\RuntimeDefinition
 * @covers ::__construct
 * @covers ::<!public>
 * @uses \Kampaw\Dic\Definition\AbstractDefinition
 * @uses \Kampaw\Dic\Definition\Parameter
 * @uses \Kampaw\Dic\Definition\Mutator
 */
class RuntimeDefinitionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers ::getConcrete
     */
    public function GetConcrete_ClassExists_ReturnsDefinition()
    {
        $result = new RuntimeDefinition('\stdClass');

        $this->assertSame('\stdClass', $result->getConcrete());
    }

    /**
     * @test
     * @covers ::getParameters
     */
    public function GetParameters_NoConstructor_NoParameters()
    {
        $result = new RuntimeDefinition('\stdClass');

        $this->assertEmpty($result->getParameters());
    }

    /**
     * @test
     * @covers ::getParameters
     */
    public function GetParameters_SingleConstructorParameter_CorrectParameterCount()
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ConcreteParameter';
        $result = new RuntimeDefinition($asset);

        $this->assertCount(1, $result->getParameters());
    }

    /**
     * @test
     * @covers ::getParameters
     */
    public function GetParameters_MultipleConstructorParameters_CorrectParameterCount()
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ConcreteAndScalarParameter';
        $result = new RuntimeDefinition($asset);

        $this->assertCount(2, $result->getParameters());
    }

    /**
     * @test
     * @covers ::getParameters
     */
    public function GetParameters_SingleConstructorParameter_CorrectParameterName()
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ConcreteParameter';
        $result = new RuntimeDefinition($asset);

        $this->assertSame('concrete', $result->getParameters()[0]->getName());
    }

    /**
     * @test
     * @covers ::getParameters
     */
    public function GetParameters_SingleConstructorParameter_CorrectParameterType()
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ConcreteParameter';
        $result = new RuntimeDefinition($asset);

        $this->assertSame('\stdClass', $result->getParameters()[0]->getType());
    }

    /**
     * @test
     * @covers ::getParameters
     */
    public function GetParameters_ConcreteAndScalarConstructorParameter_CorrectParameterTypes()
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ConcreteAndScalarParameter';
        $result = new RuntimeDefinition($asset);

        $this->assertSame('\stdClass', $result->getParameters()[0]->getType());
        $this->assertNull($result->getParameters()[1]->getType());
    }

    /**
     * @test
     * @covers ::getParameters
     */
    public function GetParameters_ConstructorParameterWithDefaultValue_CorrectParameterValue()
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ParameterWithDefaultValue';
        $result = new RuntimeDefinition($asset);

        $this->assertSame('default', $result->getParameters()[0]->getValue());
    }

    /**
     * @test
     * @covers ::getMutators
     */
    public function GetMutators_StdClassAsSubject_EmptyMutators()
    {
        $result = new RuntimeDefinition('\stdClass');

        $this->assertCount(0, $result->getMutators());
    }

    /**
     * @test
     * @covers ::getMutators
     */
    public function GetMutators_SingleMutatorNoParameters_ReturnsEmptyArray()
    {
        $asset = '\Kampaw\Dic\Assets\MutatorInjection\SingleMutatorNoParameters';
        $result = new RuntimeDefinition($asset);

        $this->assertCount(0, $result->getMutators());
    }

    /**
     * @test
     * @covers ::getMutators
     */
    public function GetMutators_SingleMutatorScalarParameter_ReturnsCorrectName()
    {
        $asset = '\Kampaw\Dic\Assets\MutatorInjection\SingleMutatorScalarParameter';
        $result = new RuntimeDefinition($asset);

        $this->assertSame('scalar', $result->getMutators()[0]->getName());
    }

    /**
     * @test
     * @covers ::getMutators
     */
    public function GetMutators_SingleMutatorConcreteParameter_ReturnsCorrectName()
    {
        $asset = '\Kampaw\Dic\Assets\MutatorInjection\SingleMutatorConcreteParameter';
        $result = new RuntimeDefinition($asset);

        $this->assertSame('concrete', $result->getMutators()[0]->getName());
    }
}