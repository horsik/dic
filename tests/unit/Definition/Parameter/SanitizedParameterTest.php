<?php

namespace Kampaw\Dic\Definition\Parameter;

/**
 * @coversDefaultClass \Kampaw\Dic\Definition\Parameter\SanitizedParameter
 * @covers ::__construct
 * @covers ::<!public>
 */
class SanitizedParameterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\DomainException
     */
    public function Construct_EmptyName_ThrowsException()
    {
        new SanitizedParameter('');
    }

    public function nonStringArgumentProvider()
    {
        return array(
            array(null),
            array(false),
            array(0xBAD),
            array(0.1),
            array(array()),
            array(new \stdClass()),
            array(tmpfile()),
            array(NAN),
            array(function() {}),
        );
    }

    /**
     * @test
     * @dataProvider nonStringArgumentProvider
     * @expectedException \Kampaw\Dic\Exception\InvalidArgumentException
     */
    public function Construct_NameNotString_ThrowsException($name)
    {
        new SanitizedParameter($name);
    }

    /**
     * @test
     * @covers ::getName
     */
    public function Construct_ValidName_NameIsSet()
    {
        $parameter = new SanitizedParameter('validName');

        $this->assertSame('validName', $parameter->getName());
    }

    public function validTypeValueProvider()
    {
        return array(
            array('array'),
            array('\stdClass'),
            array('\Countable'),
            array(__NAMESPACE__ . '\Example'),
            array(__NAMESPACE__ . '\ExampleExtended'),
            array(__NAMESPACE__ . '\ExampleInterface'),
            array(__NAMESPACE__ . '\ExampleWithInterface'),
            array(__NAMESPACE__ . '\ExampleWithInterfaceExtended'),
        );
    }

    /**
     * @test
     * @covers ::acceptsType
     * @covers ::getType
     * @dataProvider validTypeValueProvider
     */
    public function Construct_ValidTypeValue_TypeIsSet($type)
    {
        $parameter = new SanitizedParameter('exampleName', $type);

        $this->assertSame($type, $parameter->getType());
    }

    public function invalidTypeTypeProvider()
    {
        return array(
            array(false),
            array(0xBAD),
            array(0.1),
            array(array()),
            array(new \stdClass()),
            array(tmpfile()),
            array(NAN),
            array(function() {}),
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
        new SanitizedParameter('exampleName', $type);
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
        new SanitizedParameter('exampleName', $type);
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
     * @covers ::getType
     * @covers ::getValue
     * @dataProvider validDefaultValueProvider
     */
    public function Construct_ValidDefaultValue_ValueIsSet($value)
    {
        $parameter = new SanitizedParameter('exampleName', null, $value);

        $this->assertSame($value, $parameter->getValue());
    }

    public function nonArrayValueProvider()
    {
        return array(
            array(false),
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
            array(function() {}),
        );
    }

    /**
     * @test
     * @covers ::acceptsType
     * @covers ::acceptsValue
     * @covers ::getType
     * @dataProvider nonArrayValueProvider
     * @expectedException \Kampaw\Dic\Exception\InvalidArgumentException
     */
    public function Construct_ArrayTypeHintValueNotAnArray_ThrowsException($value)
    {
        new SanitizedParameter('exampleName', 'array', $value);
    }

    public function nonObjectValueProvider()
    {
        return array(
            array(false),
            array(0xBAD),
            array(0.1),
            array('invalid'),
            array(''),
            array(tmpfile()),
            array(NAN),
            array(function() {}),
        );
    }

    /**
     * @test
     * @covers ::acceptsType
     * @covers ::acceptsValue
     * @covers ::getType
     * @dataProvider nonObjectValueProvider
     * @expectedException \Kampaw\Dic\Exception\InvalidArgumentException
     */
    public function Construct_ClassTypeValueNotAnObject_ThrowsException($value)
    {
        new SanitizedParameter('exampleName', '\stdClass', $value);
    }
}