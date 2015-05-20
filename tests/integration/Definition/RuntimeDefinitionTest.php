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
     */
    public function Construct_ValidClass_ReturnsDefinition()
    {
        $result = new RuntimeDefinition('\stdClass');

        $this->assertSame('\stdClass', $result->getConcrete());
    }

    /**
     * @test
     */
    public function Construct_ClassWithoutConstructor_ReturnsDefinitionWithoutParameters()
    {
        $result = new RuntimeDefinition('\stdClass');

        $this->assertEmpty($result->getParameters());
    }

    /**
     * @test
     */
    public function Construct_SingleConstructorParameter_ReturnsDefinitionWithOneParameter()
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ConcreteParameter';
        $result = new RuntimeDefinition($asset);

        $this->assertCount(1, $result->getParameters());
    }

    /**
     * @test
     */
    public function Construct_MultipleConstructorParameters_ReturnsDefinitionWithMultipleParameters()
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\MultipleConcreteParameters';
        $result = new RuntimeDefinition($asset);

        $this->assertCount(3, $result->getParameters());
    }

    /**
     * @test
     */
    public function Construct_SingleConstructorParameter_ReturnsDefinitionWithCorrectParameterType()
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ConcreteParameter';
        $result = new RuntimeDefinition($asset);

        $this->assertSame('\stdClass', $result->getParameters()[0]->getType());
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Definition\DefinitionException
     */
    public function Construct_ScalarConstructorParameter_ThrowsException()
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ScalarParameter';
        new RuntimeDefinition($asset);
    }

    /**
     * @test
     */
    public function Construct_ScalarConstructorParameterWithDefaultValue_ReturnsDefinitionWithCorrectParameterValue()
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ScalarParameterWithDefaultValue';
        $result = new RuntimeDefinition($asset);

        $this->assertSame('default', $result->getParameters()[0]->getValue());
    }

    /**
     * @test
     */
    public function Construct_ClassWithoutMutators_ReturnsDefinitionWithoutMutators()
    {
        $result = new RuntimeDefinition('\stdClass');

        $this->assertCount(0, $result->getMutators());
    }

    /**
     * @test
     */
    public function Construct_SingleMutatorWithoutParameters_ReturnsDefinitionWithoutMutators()
    {
        $asset = '\Kampaw\Dic\Assets\MutatorInjection\SingleMutatorWithoutParameters';
        $result = new RuntimeDefinition($asset);

        $this->assertCount(0, $result->getMutators());
    }

    /**
     * @test
     */
    public function Construct_SingleMutatorWithScalarParameter_ReturnsDefinitionWithoutMutators()
    {
        $asset = '\Kampaw\Dic\Assets\MutatorInjection\SingleMutatorScalarParameter';
        $result = new RuntimeDefinition($asset);

        $this->assertCount(0, $result->getMutators());
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Definition\DefinitionException
     */
    public function Construct_ClassWithPrivateConstructor_ThrowsException()
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\PrivateConstructor';

        new RuntimeDefinition($asset);
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Definition\DefinitionException
     */
    public function Construct_AbstractClass_ThrowsException()
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\AbstractClass';

        new RuntimeDefinition($asset);
    }
}