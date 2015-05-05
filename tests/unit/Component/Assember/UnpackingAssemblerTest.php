<?php

namespace Kampaw\Dic\Component\Assembler;

/**
 * @coversDefaultClass Kampaw\Dic\Component\Assembler\UnpackingAssembler
 */
class UnpackingAssemblerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UnpackingAssembler $factory
     */
    private $factory;

    public function setUp()
    {
        if (PHP_VERSION_ID < 50600) {
            $this->markTestSkipped('PHP version too old, class under test relies on argument unpacking');
        }

        $this->factory = new UnpackingAssembler();
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
}