<?php

namespace Kampaw\Dic\Definition;

/**
 * @coversDefaultClass \Kampaw\Dic\Definition\Parameter
 * @covers ::__construct
 */
class ParameterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers ::getName
     */
    public function Construct_ExampleName_NameIsSet()
    {
        $parameter = new Parameter('name');

        $this->assertSame('name', $parameter->getName());
    }

    /**
     * @test
     * @covers ::getType
     */
    public function Construct_ExampleType_TypeIsSet()
    {
        $parameter = new Parameter(null, 'type');

        $this->assertSame('type', $parameter->getType());
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
            array(new \stdClass()),
        );
    }

    /**
     * @test
     * @covers ::getValue
     * @dataProvider validDefaultValueProvider
     */
    public function Construct_ExampleValue_ValueIsSet()
    {
        $parameter = new Parameter(null, null, 'value');

        $this->assertSame('value', $parameter->getValue());
    }

    public function validTypeProvider()
    {
        return array(
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
     * @covers ::acceptsType
     * @dataProvider validTypeProvider
     */
    public function AcceptsType_ValidType_ReturnsTrue($type)
    {
        $parameter = new Parameter('name', $type);
        $this->assertTrue($parameter->acceptsType($type));
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
    public function AcceptsType_InvalidTypeType_ReturnsFalse($type)
    {
        $parameter = new Parameter('name', $type);
        $parameter->acceptsType($type);
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
     */
    public function AcceptsType_InvalidTypeValue_ReturnsFalse($type)
    {
        $parameter = new Parameter('name', $type);
        $this->assertFalse($parameter->acceptsType($type));
    }

    public function anyValueProvider()
    {
        return array(
            array(null),
            array(10),
            array(0.1),
            array('value'),
            array(''),
            array(array()),
            array(new Example()),
            array(new ExampleExtended()),
            array(new ExampleWithInterface()),
            array(new ExampleWithInterfaceExtended()),
            array(tmpfile()),
        );
    }

    /**
     * @test
     * @covers ::acceptsValue
     * @dataProvider anyValueProvider
     */
    public function AcceptsValue_NoType_ReturnsTrue($value)
    {
        $parameter = new Parameter('name', null, $value);
        $this->assertTrue($parameter->acceptsValue($value));
    }

    public function nonArrayValueProvider()
    {
        return array(
            array(null),
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
     */
    public function AcceptsValue_ArrayTypeInvalidValue_ReturnsFalse($value)
    {
        $parameter = new Parameter('name', 'array');

        $this->assertFalse($parameter->acceptsValue($value));
    }

    /**
     * @test
     * @covers ::acceptsType
     * @covers ::acceptsValue
     */
    public function AcceptsValue_ArrayTypeValidValue_ReturnsTrue()
    {
        $parameter = new Parameter('name', 'array');

        $this->assertTrue($parameter->acceptsValue(array()));
    }

    public function nonObjectValueProvider()
    {
        return array(
            array(null),
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
     */
    public function AcceptsValue_ClassTypeValueNotAnObject_ReturnsFalse($value)
    {
        $parameter = new Parameter('name', '\Kampaw\Dic\Definition\Example');

        $this->assertFalse($parameter->acceptsValue($value));
    }

    /**
     * @test
     * @covers ::acceptsType
     * @covers ::acceptsValue
     */
    public function AcceptsValue_ClassnameMismatch_ReturnsFalse()
    {
        $parameter = new Parameter('name', '\Kampaw\Dic\Definition\Example');

        $this->assertFalse($parameter->acceptsValue(new \stdClass()));
    }

    /**
     * @test
     * @covers ::acceptsType
     * @covers ::acceptsValue
     */
    public function AcceptsValue_ClassnameMatch_ReturnsTrue()
    {
        $parameter = new Parameter('name', '\Kampaw\Dic\Definition\Example');

        $this->assertTrue($parameter->acceptsValue(new Example()));
    }

    /**
     * @test
     * @covers ::acceptsType
     * @covers ::acceptsValue
     */
    public function AcceptsValue_ValueInheritsFromType_ReturnsTrue()
    {
        $parameter = new Parameter('name', '\Kampaw\Dic\Definition\Example');

        $this->assertTrue($parameter->acceptsValue(new ExampleExtended()));
    }

    /**
     * @test
     * @covers ::acceptsType
     * @covers ::acceptsValue
     */
    public function AcceptsValue_ValueNotImplementsInterface_ReturnsFalse()
    {
        $parameter = new Parameter('name', '\Kampaw\Dic\Definition\ExampleInterface');

        $this->assertFalse($parameter->acceptsValue(new Example()));
    }

    /**
     * @test
     * @covers ::acceptsType
     * @covers ::acceptsValue
     */
    public function AcceptsValue_ValueImplementsInterface_ReturnsTrue()
    {
        $parameter = new Parameter('name', '\Kampaw\Dic\Definition\ExampleInterface');

        $this->assertTrue($parameter->acceptsValue(new ExampleWithInterface()));
    }

    /**
     * @test
     * @covers ::acceptsType
     * @covers ::acceptsValue
     */
    public function AcceptsValue_ValueInheritsInterface_ReturnsTrue()
    {
        $parameter = new Parameter('name', '\Kampaw\Dic\Definition\ExampleInterface');

        $this->assertTrue($parameter->acceptsValue(new ExampleWithInterfaceExtended()));
    }

    /**
     * @test
     * @covers ::isOptional
     */
    public function IsOptional_DefaultValueNotSet_ReturnsFalse()
    {
        $parameter = new Parameter('name');

        $this->assertFalse($parameter->isOptional());
    }

    /**
     * @test
     * @covers ::isOptional
     * @dataProvider validDefaultValueProvider
     */
    public function IsOptional_DefaultValueEvaluatesToFalse_ReturnsTrue($default)
    {
        $parameter = new Parameter('name', null, $default);

        $this->assertTrue($parameter->isOptional());
    }
}
