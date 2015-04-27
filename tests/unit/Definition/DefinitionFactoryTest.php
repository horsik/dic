<?php

namespace Kampaw\Dic\Definition;

/**
 * @coversDefaultClass \Kampaw\Dic\Definition\DefinitionFactory
 * @covers ::<!public>
 * @uses \Kampaw\Dic\Config\Configurable
 * @uses \Kampaw\Dic\Definition\Definition
 * @uses \Kampaw\Dic\Definition\Parameter
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
     * @expectedException \ReflectionException
     */
    public function GetDefinition_ClassNotExists_ReturnsNull()
    {
        $result = $this->factory->getDefinition('\nonExistent');
    }

    /**
     * @test
     * @covers ::getDefinition
     */
    public function GetDefinition_ClassExists_ReturnsDefinition()
    {
        $result = $this->factory->getDefinition('\stdClass');

        $this->assertInstanceOf(__NAMESPACE__ . '\Definition', $result);
        $this->assertSame('\stdClass', $result->getConcrete());
    }

    /**
     * @test
     * @covers ::getDefinition
     */
    public function GetDefinition_NoConstructor_NoParameters()
    {
        $result = $this->factory->getDefinition('\stdClass');

        $this->assertEmpty($result->getParameters());
    }

    /**
     * @test
     * @covers ::getDefinition
     */
    public function GetDefinition_SingleConstructorParameter_CorrectParameterCount()
    {
        $result = $this->factory->getDefinition('\Kampaw\Dic\Assets\ConstructorInjection\ConcreteParameter');

        $this->assertCount(1, $result->getParameters());
    }

    /**
     * @test
     * @covers ::getDefinition
     */
    public function GetDefinition_MultipleConstructorParameters_CorrectParameterCount()
    {
        $result = $this->factory->getDefinition('\Kampaw\Dic\Assets\ConstructorInjection\ConcreteAndScalarParameter');

        $this->assertCount(2, $result->getParameters());
    }

    /**
     * @test
     * @covers ::getDefinition
     */
    public function GetDefinition_SingleConstructorParameter_CorrectParameterName()
    {
        $result = $this->factory
                       ->getDefinition('\Kampaw\Dic\Assets\ConstructorInjection\ConcreteParameter')
                       ->getParameters();

        $this->assertSame('concrete', $result[0]->getName());
    }

    /**
     * @test
     * @covers ::getDefinition
     */
    public function GetDefinition_SingleConstructorParameter_CorrectParameterType()
    {
        $result = $this->factory
            ->getDefinition('\Kampaw\Dic\Assets\ConstructorInjection\ConcreteParameter')
            ->getParameters();

        $this->assertSame('\stdClass', $result[0]->getType());
    }

    /**
     * @test
     * @covers ::getDefinition
     */
    public function GetDefinition_ConcreteAndScalarConstructorParameter_CorrectParameterTypes()
    {
        $result = $this->factory
            ->getDefinition('\Kampaw\Dic\Assets\ConstructorInjection\ConcreteAndScalarParameter')
            ->getParameters();

        $this->assertSame('\stdClass', $result[0]->getType());
        $this->assertEmpty($result[1]->getType());
    }

    /**
     * @test
     * @covers ::getDefinition
     */
    public function GetDefinition_ConstructorParameterWithDefaultValue_CorrectParameterValue()
    {
        $result = $this->factory
            ->getDefinition('\Kampaw\Dic\Assets\ConstructorInjection\ParameterWithDefaultValue')
            ->getParameters();

        $this->assertSame('default', $result[0]->getValue());
    }
}