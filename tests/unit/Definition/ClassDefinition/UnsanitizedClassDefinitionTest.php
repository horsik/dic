<?php

namespace Kampaw\Dic\Definition\ClassDefinition;

/**
 * @coversDefaultClass \Kampaw\Dic\Definition\ClassDefinition\UnsanitizedClassDefinition
 * @covers ::<!public>
 */
class UnsanitizedClassDefinitionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers ::__construct
     * @covers ::getClass
     */
    public function Construct_ExampleClass_ClassIsSet()
    {
        $definition = new UnsanitizedClassDefinition('exampleClass');

        $this->assertSame('exampleClass', $definition->getClass());
    }
    
    /**
     * @test
     * @covers ::__construct
     * @covers ::getParameters
     */
    public function Construct_ExampleParameters_ParametersAreSet()
    {
        $definition = new UnsanitizedClassDefinition('exampleClass', array('exampleParameter'));

        $this->assertSame(array('exampleParameter'), $definition->getParameters());
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::getMutators
     */
    public function Construct_ExampleMutators_MutatorsAreSet()
    {
        $definition = new UnsanitizedClassDefinition('exampleClass', array(), array('exampleMutator'));

        $this->assertSame(array('exampleMutator'), $definition->getMutators());
    }
}