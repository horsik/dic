<?php

namespace Kampaw\Dic\Definition\InterfaceDefinition;

use Kampaw\Dic\Definition\ClassDefinition\UnsanitizedClassDefinition;

/**
 * @coversDefaultClass \Kampaw\Dic\Definition\InterfaceDefinition\UnsanitizedInterfaceDefinition
 * @covers ::__construct
 * @covers ::<!public>
 * @uses \Kampaw\Dic\Definition\InterfaceDefinition\AbstractInterfaceDefinition
 */
class UnsanitizedInterfaceDefinitionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers ::getInterface
     */
    public function GetInterface_InterfaceSet_ReturnsInterface()
    {
        $definition = new UnsanitizedInterfaceDefinition('exampleInterface', array());

        $this->assertSame('exampleInterface', $definition->getInterface());
    }

    /**
     * @test
     * @covers ::getParameters
     * @uses \Kampaw\Dic\Definition\ClassDefinition\AbstractClassDefinition
     * @uses \Kampaw\Dic\Definition\ClassDefinition\UnsanitizedClassDefinition
     */
    public function GetParameters_ParametersSet_ReturnsParameters()
    {
        $candidate = new UnsanitizedClassDefinition('exampleClass', array('exampleParameter'));
        $definition = new UnsanitizedInterfaceDefinition('exampleInterface', array($candidate));

        $this->assertSame(array('exampleParameter'), $definition->getParameters());
    }

    /**
     * @test
     * @covers ::getCandidates
     * @uses \Kampaw\Dic\Definition\ClassDefinition\AbstractClassDefinition
     * @uses \Kampaw\Dic\Definition\ClassDefinition\UnsanitizedClassDefinition
     */
    public function GetCandidates_CandidatesAreSet_ReturnsCandidates()
    {
        $candidates = array(
            new UnsanitizedClassDefinition('first'),
            new UnsanitizedClassDefinition('second'),
        );

        $definition = new UnsanitizedInterfaceDefinition('exampleInterface', $candidates);

        $this->assertSame($candidates, $definition->getCandidates());

    }
}