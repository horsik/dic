<?php

namespace Kampaw\Dic\Component\Assembler;

/**
 * @coversDefaultClass \Kampaw\Dic\Component\Assembler\SmartAssembler
 */
class SmartAssemblerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SmartAssembler $factory
     */
    private $factory;

    public function setUp()
    {
        $this->factory = new SmartAssembler();
    }

    /**
     * @test
     * @covers ::getInstance
     */
    public function GetInstance_ClassWithNoArguments_ReturnsInstance()
    {
        $result = $this->factory->getInstance('\stdClass');

        $this->assertInstanceOf('\stdClass', $result);
    }

    /**
     * @test
     * @covers ::getInstance
     */
    public function GetInstance_ConcreteDependency_ReturnsInstance()
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ConcreteParameter';
        $result = $this->factory->getInstance($asset, array(new \stdClass()));

        $this->assertInstanceOf($asset, $result);
    }

    /**
     * @test
     * @covers ::getInstance
     * @uses \Kampaw\Dic\Component\Assembler\ReflectionAssembler
     */
    public function GetInstance_ThreeConcreteParameters_ReturnsInstance()
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ThreeConcreteParameters';

        $arguments = array(
            new \stdClass(),
            new \stdClass(),
            new \stdClass(),
        );

        $result = $this->factory->getInstance($asset, $arguments);

        $this->assertInstanceOf($asset, $result);
    }
} 