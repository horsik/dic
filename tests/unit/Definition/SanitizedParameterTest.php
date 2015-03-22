<?php

namespace Kampaw\Dic\Definition;

/**
 * @coversDefaultClass \Kampaw\Dic\Definition\SanitizedParameter
 * @covers ::__construct
 * @covers ::<!public>
 * @uses \Kampaw\Dic\Definition\Parameter
 */
class SanitizedParameterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\DomainException
     */
    public function Construct_EmptyName_ThrowsException()
    {
        $parameter = new Parameter('');
        new SanitizedParameter($parameter);
    }

    public function nonStringArgumentProvider()
    {
        return array(
            array(null),
            array(0xBAD),
            array(0.1),
            array(array()),
            array(new \stdClass()),
            array(tmpfile()),
            array(NAN),
        );
    }

    /**
     * @test
     * @dataProvider nonStringArgumentProvider
     * @expectedException \Kampaw\Dic\Exception\InvalidArgumentException
     */
    public function Construct_NameNotString_ThrowsException($name)
    {
        $parameter = new Parameter($name);
        new SanitizedParameter($parameter);
    }

    /**
     * @test
     * @covers ::getName
     */
    public function Construct_ValidName_NameIsSet()
    {
        $parameter = new Parameter('validName');
        $parameter = new SanitizedParameter($parameter);

        $this->assertSame('validName', $parameter->getName());
    }

    public function validTypeValueProvider()
    {
        return array(
            array(null),
            array('array'),
            array('\stdClass'),
            array('\Countable'),
            array('\Kampaw\Dic\Definition\Example'),
            array('\Kampaw\Dic\Definition\ExampleExtended'),
            array('\Kampaw\Dic\Definition\ExampleInterface'),
            array('\Kampaw\Dic\Definition\ExampleWithInterface'),
            array('\Kampaw\Dic\Definition\ExampleWithInterfaceExtended'),
        );
    }

    /**
     * @test
     * @covers ::getName
     * @covers ::acceptsType
     * @dataProvider validTypeValueProvider
     */
    public function Construct_ValidTypeValue_TypeIsSet($type)
    {
        $parameter = new Parameter('name', $type);
        $parameter = new SanitizedParameter($parameter);

        $this->assertSame($type, $parameter->getType());
    }

    public function invalidTypeTypeProvider()
    {
        return array(
            array(0xBAD),
            array(0.1),
            array(array()),
            array(new \stdClass()),
            array(tmpfile()),
            array(NAN),
        );
    }

    /**
     * @test
     * @covers ::acceptsType
     * @dataProvider invalidTypeTypeProvider
     * @expectedException \Kampaw\Dic\Exception\InvalidArgumentException
     */
    public function Construct_InvalidTypeType_ThrowsException($type)
    {
        $parameter = new Parameter('name', $type);
        new SanitizedParameter($parameter);
    }

    public function invalidTypeValueProvider()
    {
        return array(
            array('gibberish'),
            array('\nonExistentClass'),
            array('\nonExistentInterface'),
        );
    }

    /**
     * @test
     * @covers ::acceptsType
     * @dataProvider invalidTypeValueProvider
     * @expectedException \Kampaw\Dic\Exception\DomainException
     */
    public function Construct_InvalidTypeValue_ThrowsException($type)
    {
        $parameter = new Parameter('name', $type);
        new SanitizedParameter($parameter);
    }

    public function validDefaultValueProvider()
    {
        return array(
            array('default'),
            array(''),
            array('0'),
            array(1),
            array(0),
            array(1.1),
            array(0.0),
            array(array('default')),
            array(array()),
            array(true),
            array(false),
            array(null),
            array(new Example()),
            array(new ExampleExtended()),
            array(new ExampleWithInterface()),
            array(new ExampleWithInterfaceExtended()),
        );
    }

    /**
     * @test
     * @covers ::acceptsValue
     * @covers ::getValue
     * @dataProvider validDefaultValueProvider
     */
    public function Construct_ValidDefaultValue_ValueIsSet($value)
    {
        $parameter = new Parameter('name', null, $value);
        $parameter = new SanitizedParameter($parameter);

        $this->assertSame($value, $parameter->getValue());
    }

    public function nonArrayValueProvider()
    {
        return array(
            array(0xBAD),
            array(0.1),
            array('invalid'),
            array(''),
            array(new Example()),
            array(new ExampleExtended()),
            array(new ExampleWithInterface()),
            array(new ExampleWithInterfaceExtended()),
            array(tmpfile()),
            array(NAN),
        );
    }

    /**
     * @test
     * @covers ::acceptsType
     * @covers ::acceptsValue
     * @dataProvider nonArrayValueProvider
     * @expectedException \Kampaw\Dic\Exception\InvalidArgumentException
     */
    public function Construct_ArrayTypeHintValueNotAnArray_ThrowsException($value)
    {
        $parameter = new Parameter('name', 'array', $value);
        new SanitizedParameter($parameter);
    }

    public function nonObjectValueProvider()
    {
        return array(
            array(0xBAD),
            array(0.1),
            array('invalid'),
            array(''),
            array(tmpfile()),
            array(NAN),
        );
    }

    /**
     * @test
     * @covers ::acceptsType
     * @covers ::acceptsValue
     * @dataProvider nonObjectValueProvider
     * @expectedException \Kampaw\Dic\Exception\InvalidArgumentException
     */
    public function Construct_ClassTypeValueNotAnObject_ThrowsException($value)
    {
        $parameter = new Parameter('name', '\stdClass', $value);
        new SanitizedParameter($parameter);
    }

    /**
     * @test
     * @covers ::isOptional
     */
    public function IsOptional_DefaultValueNotSet_ReturnsFalse()
    {
        $parameter = new Parameter('name');
        $parameter = new SanitizedParameter($parameter);

        $this->assertFalse($parameter->isOptional());
    }

    /**
     * @test
     * @covers ::setValue
     * @covers ::isOptional
     * @dataProvider validDefaultValueProvider
     */
    public function IsOptional_DefaultValueEvaluatesToFalse_ReturnsTrue($default)
    {
        $parameter = new Parameter('name', null, $default);
        $parameter = new SanitizedParameter($parameter);

        $this->assertTrue($parameter->isOptional());
    }
}