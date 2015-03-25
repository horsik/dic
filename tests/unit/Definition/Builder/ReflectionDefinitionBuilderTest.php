<?php

namespace Kampaw\Dic\Definition\Builder;

/**
 * @coversDefaultClass \Kampaw\Dic\Definition\Builder\ReflectionDefinitionBuilder
 * @covers ::<!public>
 * @uses \Kampaw\Dic\Definition\ClassDefinition\AbstractClassDefinition
 * @uses \Kampaw\Dic\Definition\ClassDefinition\UnsanitizedClassDefinition
 * @uses \Kampaw\Dic\Definition\Parameter\AbstractParameter
 * @uses \Kampaw\Dic\Definition\Parameter\UnsanitizedParameter
 */
class ReflectionDefinitionBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReflectionDefinitionBuilder $builder
     */
    private $builder;

    public function setUp()
    {
        $this->builder = new ReflectionDefinitionBuilder();
    }

    /**
     * @test
     * @covers ::getClassDefinition
     */
    public function GetClassDefinition_StdClass_ReturnsDefinitionWithNameAndNoParameters()
    {
        $definition = $this->builder->getClassDefinition('stdClass');

        $this->assertSame('\stdClass', $definition->getClass());
        $this->assertCount(0, $definition->getParameters());
    }

    /**
     * @test
     * @covers ::getClassDefinition
     * @expectedException \Kampaw\Dic\Exception\DomainException
     */
    public function GetClassDefinition_NonExistentClass_ThrowsException()
    {
        $this->builder->getClassDefinition('NonExistent');
    }

    /**
     * @test
     * @covers ::getClassDefinition
     */
    public function GetClassDefinition_OneClassParameter_ReturnsDefinitionWithParameter()
    {
        $definition = $this->builder->getClassDefinition(__NAMESPACE__ . '\CtorClassParam');
        $parameters = $definition->getParameters();
        $result = array_pop($parameters);

        $this->assertSame('stdClass', $result->getType());
        $this->assertSame('exampleName', $result->getName());
        $this->assertNull($result->getValue());
    }

    /**
     * @test
     * @covers ::getClassDefinition
     */
    public function GetClassDefinition_OneScalarParameter_ReturnsDefinitionWithParameter()
    {
        $definition = $this->builder->getClassDefinition(__NAMESPACE__ . '\CtorScalarParam');
        $parameters = $definition->getParameters();
        $result = array_pop($parameters);

        $this->assertSame('scalar', $result->getName());
        $this->assertNull($result->getType());
        $this->assertNull($result->getValue());
    }

    /**
     * @test
     * @covers ::getClassDefinition
     */
    public function GetClassDefinition_OneScalarParameterWithValue_ReturnsDefinitionWithParameter()
    {
        $definition = $this->builder->getClassDefinition(__NAMESPACE__ . '\CtorScalarValueParam');
        $parameters = $definition->getParameters();
        $result = array_pop($parameters);

        $this->assertSame('scalar', $result->getName());
        $this->assertSame(10, $result->getValue());
        $this->assertNull($result->getType());
    }
}