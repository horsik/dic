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
        $result = new RuntimeDefinition('stdClass');

        $this->assertSame('stdClass', $result->getConcrete());
    }

    /**
     * @test
     */
    public function Construct_ClassWithoutAConstructor_ReturnsDefinitionWithoutParameters()
    {
        $result = new RuntimeDefinition('stdClass');

        $this->assertEmpty($result->getParameters());
    }

    /**
     * @test
     */
    public function Construct_ConcreteConstructorParameter_ReturnsDefinitionWithOneParameter()
    {
        $asset = 'Kampaw\Dic\Assets\ConstructorInjection\ConcreteParameter';
        $result = new RuntimeDefinition($asset);

        $this->assertCount(1, $result->getParameters());
    }

    /**
     * @test
     */
    public function Construct_MultipleConcreteConstructorParameters_ReturnsDefinitionWithMultipleParameters()
    {
        $asset = 'Kampaw\Dic\Assets\ConstructorInjection\MultipleConcreteParameters';
        $result = new RuntimeDefinition($asset);

        $this->assertCount(3, $result->getParameters());
    }

    /**
     * @test
     */
    public function Construct_ConcreteConstructorParameter_ReturnsDefinitionWithCorrectParameterType()
    {
        $asset = 'Kampaw\Dic\Assets\ConstructorInjection\ConcreteParameter';
        $result = new RuntimeDefinition($asset);

        $this->assertSame('stdClass', $result->getParameters()[0]->getType());
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Definition\DefinitionException
     */
    public function Construct_ScalarConstructorParameter_ThrowsException()
    {
        $asset = 'Kampaw\Dic\Assets\ConstructorInjection\ScalarParameter';

        new RuntimeDefinition($asset);
    }

    /**
     * @test
     */
    public function Construct_ScalarConstructorParameterWithDefaultValue_ReturnsDefinitionWithCorrectParameterValue()
    {
        $asset = 'Kampaw\Dic\Assets\ConstructorInjection\ScalarParameterWithDefaultValue';
        $result = new RuntimeDefinition($asset);

        $this->assertSame('default', $result->getParameters()[0]->getValue());
    }

    /**
     * @test
     */
    public function Construct_ClassWithoutMutators_ReturnsDefinitionWithoutMutators()
    {
        $result = new RuntimeDefinition('stdClass');

        $this->assertCount(0, $result->getMutators());
    }

    /**
     * @test
     */
    public function Construct_MutatorWithoutParameters_ReturnsDefinitionWithoutMutators()
    {
        $asset = 'Kampaw\Dic\Assets\MutatorInjection\WithoutParameters';
        $result = new RuntimeDefinition($asset);

        $this->assertCount(0, $result->getMutators());
    }

    /**
     * @test
     */
    public function Construct_ScalarMutatorParameter_ReturnsDefinitionWithoutMutators()
    {
        $asset = 'Kampaw\Dic\Assets\MutatorInjection\ScalarParameter';
        $result = new RuntimeDefinition($asset);

        $this->assertCount(0, $result->getMutators());
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Definition\DefinitionException
     */
    public function Construct_ClassWithPrivateConstructor_ThrowsException()
    {
        $asset = 'Kampaw\Dic\Assets\ConstructorInjection\PrivateConstructor';

        new RuntimeDefinition($asset);
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Definition\DefinitionException
     */
    public function Construct_AbstractClass_ThrowsException()
    {
        $asset = 'Kampaw\Dic\Assets\ConstructorInjection\AbstractClass';

        new RuntimeDefinition($asset);
    }

    /**
     * @test
     */
    public function Construct_ConcreteMutatorParameter_ReturnsDefinitionWithCorrectMutatorName()
    {
        $asset = 'Kampaw\Dic\Assets\MutatorInjection\ConcreteParameter';
        $result = new RuntimeDefinition($asset);

        $this->assertSame('setConcrete', $result->getMutators()[0]->getName());
    }

    /**
     * @test
     */
    public function Construct_ConcreteMutatorParameter_ReturnsDefinitionWithCorrectMutatorType()
    {
        $asset = 'Kampaw\Dic\Assets\MutatorInjection\ConcreteParameter';
        $result = new RuntimeDefinition($asset);

        $this->assertSame('stdClass', $result->getMutators()[0]->getType());
    }

    /**
     * @test
     */
    public function Construct_PrivateMutatorConcreteParameter_ReturnsDefinitionWithoutMutators()
    {
        $asset = 'Kampaw\Dic\Assets\MutatorInjection\PrivateMethod';
        $result = new RuntimeDefinition($asset);

        $this->assertCount(0, $result->getMutators());
    }

    /**
     * @test
     */
    public function Construct_ConcreteConstructorParameterWithDefaultValue_ReturnsDefinitionWithCorrectParameterValue()
    {
        $asset = 'Kampaw\Dic\Assets\ConstructorInjection\ConcreteParameterWithDefaultValue';
        $result = new RuntimeDefinition($asset);

        $this->assertSame('stdClass', $result->getParameters()[0]->getType());
        $this->assertTrue($result->getParameters()[0]->isOptional());
    }

    /**
     * @test
     */
    public function Construct_MultipleConcreteConstructorParameters_ReturnsDefinitionWithoutMutators()
    {
        $asset = 'Kampaw\Dic\Assets\MutatorInjection\MultipleParameters';
        $result = new RuntimeDefinition($asset);

        $this->assertCount(0, $result->getMutators());
    }
}