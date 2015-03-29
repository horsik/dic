<?php

namespace Kampaw\Dic\Definition\ClassDefinition;

/**
 * @coversDefaultClass \Kampaw\Dic\Definition\ClassDefinition\AbstractClassDefinition
 * @covers ::<!public>
 */
class AbstractClassDefinitionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractClassDefinition $definition
     */
    private $definition;

    public function setUp()
    {
        $this->definition = $this->getMockBuilder(__NAMESPACE__ . '\AbstractClassDefinition')
             ->disableOriginalConstructor()
             ->getMockForAbstractClass();
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function Construct_ExampleClass_PassedToSetter()
    {
        $this->definition
             ->expects($this->once())
             ->method('setClass')
             ->with('exampleClass');

        $this->definition->__construct('exampleClass');
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function Construct_ExampleParameters_PassedToSetter()
    {
        $this->definition
             ->expects($this->once())
             ->method('addParameter')
             ->with('exampleParameter');

        $this->definition->__construct('exampleClass', array('exampleParameter'));
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function Construct_ExampleMutators_PassedToSetter()
    {
        $this->definition
             ->expects($this->once())
             ->method('setMutators')
             ->with(array('exampleMutator'));

        $this->definition->__construct('exampleClass', array(), array('exampleMutator'));
    }
}