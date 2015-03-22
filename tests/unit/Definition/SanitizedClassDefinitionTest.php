<?php

namespace Kampaw\Dic\Definition;

/**
 * @coversDefaultClass \Kampaw\Dic\Definition\SanitizedClassDefinition
 * @covers ::__construct
 * @covers ::setClass
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
        );
    }

    /**
     * @test
     * @dataProvider nonStringProvider
     * @expectedException \Kampaw\Dic\Exception\InvalidArgumentException
     */
    public function Construct_InvalidClass_ThrowsException($class)
    {
        new SanitizedClassDefinition(new ClassDefinition($class));
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\DomainException
     */
    public function Construct_EmptyClass_ThrowsException()
    {
        new SanitizedClassDefinition(new ClassDefinition(''));
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\DomainException
     */
    public function Construct_NonExistentClass_ThrowsException()
    {
        new SanitizedClassDefinition(new ClassDefinition('\NonExistentClass'));
    }

    /**
     * @test
     */
    public function Construct_ExistentClass_ClassIsSet()
    {
        $definition = new SanitizedClassDefinition(new ClassDefinition('\stdClass'));

        $this->assertSame('\stdClass', $definition->getClass());
    }

    /**
     * @test
     */
    public function Construct_ExistentClassNoPrecedingSlash_PrependSlash()
    {
        $definition = new SanitizedClassDefinition(new ClassDefinition('stdClass'));

        $this->assertSame('\stdClass', $definition->getClass());
    }

    /**
     * @return array
     */
    public function invalidParameterTypeProvider()
    {
        return array(
            array(null),
            array(false),
            array(0),
            array(0xBAD),
            array(0.1),
            array(''),
            array('invalid'),
            array(NAN),
            array(array()),
            array(new \stdClass()),
            array(tmpfile()),
        );
    }

    /**
     * @test
     * @covers ::setParameters
     * @dataProvider invalidParameterTypeProvider
     * @expectedException \Kampaw\Dic\Exception\InvalidArgumentException
     */
    public function Construct_ParametersContainsInvalidType_ThrowsException($parameter)
    {
        new SanitizedClassDefinition(new ClassDefinition('stdClass', array($parameter)));
    }

    public function validParametersProvider()
    {
        return array(
            array(array(new Parameter('name'))),
            array(array(new Parameter('param1'), new Parameter('param2'))),
            array(array(new SanitizedParameter(new Parameter('name')))),
            array(array(new SanitizedParameter(new Parameter('param1')), new Parameter('param2'))),
            array(array(new SanitizedParameter(new Parameter('param1')), new SanitizedParameter(new Parameter('param2')))),
        );
    }

    /**
     * @test
     * @covers ::getParameters
     * @covers ::setParameters
     * @uses \Kampaw\Dic\Definition\Parameter
     * @dataProvider validParametersProvider
     */
    public function Construct_ValidParameters_ParametersAreSet($parameters)
    {
        $definition = new ClassDefinition('stdClass', $parameters);
        $definition = new SanitizedClassDefinition($definition);

        $this->assertSame($parameters, $definition->getParameters());
    }

    /**
     * @test
     * @covers ::setParameters
     * @uses \Kampaw\Dic\Definition\Parameter
     * @expectedException \Kampaw\Dic\Exception\InvalidArgumentException
     */
    public function Construct_ParameterNameColision_ThrowsException()
    {
        $param1 = new Parameter('colision');
        $param2 = new Parameter('colision');

        new SanitizedClassDefinition(new ClassDefinition('stdClass', array($param1, $param2)));
    }
}