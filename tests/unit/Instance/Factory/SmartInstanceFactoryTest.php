<?php

namespace Kampaw\Dic\Instance\Factory;

/**
 * @coversDefaultClass \Kampaw\Dic\Instance\Factory\SmartInstanceFactory
 */
class SmartInstanceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SmartInstanceFactory $factory
     */
    private $factory;

    public function setUp()
    {
        $this->factory = new SmartInstanceFactory();
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
     * @uses \Kampaw\Dic\Instance\Factory\ReflectionInstanceFactory
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