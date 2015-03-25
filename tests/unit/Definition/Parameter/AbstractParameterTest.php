<?php

namespace Kampaw\Dic\Definition\Parameter;

/**
 * @coversDefaultClass \Kampaw\Dic\Definition\Parameter\AbstractParameter
 */
class AbstractParameterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractParameter $parameter
     */
    private $parameter;

    public function setUp()
    {
        $this->parameter = $this->getMockBuilder(__NAMESPACE__ . '\AbstractParameter')
             ->disableOriginalConstructor()
             ->setMethods(array('getType'))
             ->getMockForAbstractClass();
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function Construct_ExampleName_PassedToSetter()
    {
        $this->parameter
             ->expects($this->once())
             ->method('setName')
             ->with('exampleName');

        $this->parameter->__construct('exampleName');
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function Construct_ExampleType_PassedToSetter()
    {
        $this->parameter
             ->expects($this->once())
             ->method('setType')
             ->with('exampleType');

        $this->parameter->__construct('exampleName', 'exampleType');
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
     * @covers ::__construct
     * @dataProvider validDefaultValueProvider
     */
    public function Construct_ExampleValue_PassedToSetter($value)
    {
        $this->parameter
             ->expects($this->once())
             ->method('setValue')
             ->with($value);

        $this->parameter->__construct('exampleName', 'exampleType', $value);
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function Construct_WithoutValueArgument_SetterNotCalled()
    {
        $this->parameter->expects($this->never())->method('setValue');

        $this->parameter->__construct('exampleName', 'exampleType');
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
     * @dataProvider validTypeValueProvider
     */
    public function AcceptsType_ValidTypeValue_ReturnsTrue($type)
    {
        $this->assertTrue($this->parameter->acceptsType($type));
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
    public function AcceptsType_InvalidTypeType_ThrowsException($type)
    {
        $this->parameter->acceptsType($type);
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
        $this->assertFalse($this->parameter->acceptsType($type));
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::isOptional
     */
    public function IsOptional_DefaultValueNotSet_ReturnsFalse()
    {
        $this->parameter->__construct('exampleName', 'exampleType');

        $this->assertFalse($this->parameter->isOptional());
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::isOptional
     * @dataProvider validDefaultValueProvider
     */
    public function IsOptional_DefaultValueEvaluatesToFalse_ReturnsTrue($default)
    {
        $this->parameter->__construct('exampleName', 'exampleType', $default);

        $this->assertTrue($this->parameter->isOptional());
    }

    public function anyValueProvider()
    {
        return array(
            array(null),
            array(false),
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
            array(NAN),
            array(function() {})
        );
    }

    /**
     * @test
     * @covers ::acceptsValue
     * @dataProvider anyValueProvider
     */
    public function AcceptsValue_AnyValueNoType_ReturnsTrue($value)
    {
        $this->parameter->method('getType')->willReturn(null);

        $this->assertTrue($this->parameter->acceptsValue($value));
    }

    public function nonArrayValueProvider()
    {
        return array(
            array(false),
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
     * @covers ::acceptsValue
     * @dataProvider nonArrayValueProvider
     */
    public function AcceptsValue_ArrayTypeInvalidValue_ReturnsFalse($value)
    {
        $this->parameter->method('getType')->willReturn('array');

        $this->assertFalse($this->parameter->acceptsValue($value));
    }

    /**
     * @test
     * @covers ::acceptsValue
     */
    public function AcceptsValue_ArrayTypeValidValue_ReturnsTrue()
    {
        $this->parameter->method('getType')->willReturn('array');

        $this->assertTrue($this->parameter->acceptsValue(array()));
    }

    public function nonObjectValueProvider()
    {
        return array(
            array(null),
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
     * @covers ::acceptsValue
     * @dataProvider nonObjectValueProvider
     */
    public function AcceptsValue_TypeClassValueNotAnObject_ReturnsFalse($value)
    {
        $this->parameter->method('getType')->willReturn(__NAMESPACE__ . '\Example');

        $this->assertFalse($this->parameter->acceptsValue($value));
    }

    /**
     * @test
     * @covers ::acceptsValue
     */
    public function AcceptsValue_ClassnameMismatch_ReturnsFalse()
    {
        $this->parameter->method('getType')->willReturn(__NAMESPACE__ . '\Example');

        $this->assertFalse($this->parameter->acceptsValue(new \stdClass()));
    }

    /**
     * @test
     * @covers ::acceptsValue
     */
    public function AcceptsValue_ClassnameMatch_ReturnsTrue()
    {
        $this->parameter->method('getType')->willReturn(__NAMESPACE__ . '\Example');

        $this->assertTrue($this->parameter->acceptsValue(new Example()));
    }

    /**
     * @test
     * @covers ::acceptsValue
     */
    public function AcceptsValue_ValueInheritsFromType_ReturnsTrue()
    {
        $this->parameter->method('getType')->willReturn(__NAMESPACE__ . '\Example');

        $this->assertTrue($this->parameter->acceptsValue(new ExampleExtended()));
    }

    /**
     * @test
     * @covers ::acceptsValue
     */
    public function AcceptsValue_ValueNotImplementsInterface_ReturnsFalse()
    {
        $this->parameter->method('getType')->willReturn(__NAMESPACE__ . '\ExampleInterface');

        $this->assertFalse($this->parameter->acceptsValue(new Example()));
    }

    /**
     * @test
     * @covers ::acceptsValue
     */
    public function AcceptsValue_ValueImplementsInterface_ReturnsTrue()
    {
        $this->parameter->method('getType')->willReturn(__NAMESPACE__ . '\ExampleInterface');

        $this->assertTrue($this->parameter->acceptsValue(new ExampleWithInterface()));
    }

    /**
     * @test
     * @covers ::acceptsValue
     */
    public function AcceptsValue_ValueInheritsInterface_ReturnsTrue()
    {
        $this->parameter->method('getType')->willReturn(__NAMESPACE__ . '\ExampleInterface');

        $this->assertTrue($this->parameter->acceptsValue(new ExampleWithInterfaceExtended()));
    }
}