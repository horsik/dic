<?php

namespace Kampaw\Dic\Definition;

/**
 * @coversDefaultClass \Kampaw\Dic\Definition\InterfaceDefinition
 * @covers ::__construct
 */
class InterfaceDefinitionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function Construct_ExampleInterface_InterfaceIsSet()
    {
        $definition = new InterfaceDefinition('\ExampleInterface', array());

        $this->assertSame('\ExampleInterface', $definition->getInterface());
    }
}