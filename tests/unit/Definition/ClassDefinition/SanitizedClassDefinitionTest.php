<?php

namespace Kampaw\Dic\Definition\ClassDefinition;

use Kampaw\Dic\Definition\Parameter\SanitizedParameter;
use Kampaw\Dic\Definition\Parameter\UnsanitizedParameter;

/**
 * @coversDefaultClass \Kampaw\Dic\Definition\ClassDefinition\SanitizedClassDefinition
 * @covers ::__construct
 * @covers ::<!public>
 */
class SanitizedClassDefinitionTest extends \PHPUnit_Framework_TestCase
{
    public function nonStringProvider()
    {
        return array(
            array(true),
            array(false),
            array(null),
            array(0),
            array(0xBAD),
            array(0.1),
            array(NAN),
            array(array()),
            array(new \stdClass()),
            array(tmpfile()),
            array(function() {}),
        );
    }

    /**
     * @test
     * @dataProvider nonStringProvider
     * @expectedException \Kampaw\Dic\Exception\InvalidArgumentException
     */
    public function Construct_InvalidClassType_ThrowsException($class)
    {
        new SanitizedClassDefinition($class);
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\DomainException
     */
    public function Construct_EmptyClass_ThrowsException()
    {
        new SanitizedClassDefinition('');
    }

    public function invalidClassValueProvider()
    {
        return array(
            array('gibberish'),
            array('\nonExistentClass'),
            array('\ArrayAccess'),
        );
    }

    /**
     * @test
     * @dataProvider invalidClassValueProvider
     * @expectedException \Kampaw\Dic\Exception\DomainException
     */
    public function Construct_InvalidClassValue_ThrowsException($class)
    {
        new SanitizedClassDefinition($class);
    }

    /**
     * @test
     * @covers ::getClass
     */
    public function Construct_ValidClassValue_ClassIsSet()
    {
        $definition = new SanitizedClassDefinition('\stdClass');

        $this->assertSame('\stdClass', $definition->getClass());
    }

    /**
     * @test
     * @covers ::getClass
     */
    public function Construct_ValidClassNoPrecedingSlash_PrependSlash()
    {
        $definition = new SanitizedClassDefinition('stdClass');

        $this->assertSame('\stdClass', $definition->getClass());
    }

    /**
     * @test
     * @covers ::getParameters
     * @uses \Kampaw\Dic\Definition\Parameter\SanitizedParameter
     * @uses \Kampaw\Dic\Definition\Parameter\AbstractParameter
     */
    public function Construct_ValidParameter_ParametersAreSet()
    {
        $parameter = new SanitizedParameter('exampleParameter');
        $definition = new SanitizedClassDefinition('\stdClass', array($parameter));

        $result = current($definition->getParameters());

        $this->assertSame('exampleParameter', $result->getName());
    }

    /**
     * @test
     * @covers ::getParameters
     * @uses \Kampaw\Dic\Definition\Parameter\UnsanitizedParameter
     * @uses \Kampaw\Dic\Definition\Parameter\SanitizedParameter
     * @uses \Kampaw\Dic\Definition\Parameter\AbstractParameter
     */
    public function Construct_UnsanitizedValidParameter_ParameterSanitizedAndSet()
    {
        $parameter = new UnsanitizedParameter('exampleParameter');
        $definition = new SanitizedClassDefinition('\stdClass', array($parameter));

        $result = current($definition->getParameters());

        $this->assertInstanceOf('\Kampaw\Dic\Definition\Parameter\SanitizedParameter', $result);
        $this->assertSame('exampleParameter', $result->getName());
    }

    /**
     * @test
     * @covers ::getParameters
     * @uses \Kampaw\Dic\Definition\Parameter\UnsanitizedParameter
     * @uses \Kampaw\Dic\Definition\Parameter\SanitizedParameter
     * @uses \Kampaw\Dic\Definition\Parameter\AbstractParameter
     * @expectedException \Kampaw\Dic\Exception\ExceptionInterface
     */
    public function Construct_UnsanitizedInvalidParameter_ThrowsException()
    {
        $parameter = new UnsanitizedParameter('exampleParameter', 'invalidType');
        new SanitizedClassDefinition('\stdClass', array($parameter));
    }

    /**
     * @test
     * @uses \Kampaw\Dic\Definition\Parameter\SanitizedParameter
     * @uses \Kampaw\Dic\Definition\Parameter\AbstractParameter
     * @expectedException \Kampaw\Dic\Exception\BadMethodCallException
     */
    public function Construct_ParameterNameCollision_ThrowsException()
    {
        $param1 = new SanitizedParameter('collision');
        $param2 = new SanitizedParameter('collision');

        new SanitizedClassDefinition('stdClass', array($param1, $param2));
    }
}