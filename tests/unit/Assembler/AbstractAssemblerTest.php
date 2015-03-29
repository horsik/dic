<?php

namespace Kampaw\Dic\Assembler;

use Kampaw\Dic\Definition\ClassDefinition\UnsanitizedClassDefinition;
use Kampaw\Dic\Definition\InterfaceDefinition\UnsanitizedInterfaceDefinition;
use Kampaw\Dic\Definition\Parameter\UnsanitizedParameter;
use Kampaw\Dic\Definition\Parameter\Example;
use Kampaw\Dic\Definition\Parameter\ExampleExtended;
use Kampaw\Dic\Definition\Parameter\ExampleWithInterface;
use Kampaw\Dic\Definition\Parameter\ExampleWithInterfaceExtended;

/**
 * @coversDefaultClass \Kampaw\Dic\Assembler\AbstractAssembler
 * @covers ::<!public>
 * @covers ::__construct
 * @uses \Kampaw\Dic\Definition\ClassDefinition\AbstractClassDefinition
 * @uses \Kampaw\Dic\Definition\ClassDefinition\UnsanitizedClassDefinition
 * @uses \Kampaw\Dic\Definition\Parameter\AbstractParameter
 * @uses \Kampaw\Dic\Definition\Parameter\UnsanitizedParameter
 */
class AbstractAssemblerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Kampaw\Dic\Assembler\AbstractAssembler $assembler
     */
    private $assembler;

    /**
     * @var \Kampaw\Dic\DefinitionRepository $repository
     */
    private $repository;

    public function setUp()
    {
        $this->repository = $this->getMockBuilder('Kampaw\Dic\DefinitionRepository')
             ->setMethods(array())
             ->getMock();

        $this->assembler = $this->getMockForAbstractClass(
            'Kampaw\Dic\Assembler\AbstractAssembler',
            array($this->repository)
        );

        $this->assembler
             ->method('createInstance')
             ->will($this->returnCallback(function() { return func_get_args(); }));
    }

    public function addDefinitions()
    {
        $values = array();

        foreach (func_get_args() as $value) {
            $values[] = array($value->getClass(), $value);
        }

        $this->repository
            ->method('get')
            ->will($this->returnValueMap($values));
    }

    /**
     * @test
     * @covers ::assemble
     * @expectedException \Kampaw\Dic\Exception\LogicException
     */
    public function Assemble_ConcreteDefinitionNotRegistered_ThrowsException()
    {
        $this->assembler->assemble('\ExampleClass');
    }

    /**
     * @test
     * @covers ::assemble
     */
    public function Assemble_ConcreteDefinition_PassedDefinition()
    {
        $target = new UnsanitizedClassDefinition('\ExampleClass');
        $this->addDefinitions($target);

        $result = $this->assembler->assemble('\ExampleClass');

        $this->assertSame($target, $result[0]);
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
        );
    }

    /**
     * @test
     * @covers ::assemble
     * @dataProvider validDefaultValueProvider
     */
    public function Assemble_ParameterWithDefaultValueNoUserArguments_PassedDefaultValue($default)
    {
        $param1 = new UnsanitizedParameter('someName', null, $default);
        $target = new UnsanitizedClassDefinition('\ExampleClass', array($param1));

        $this->addDefinitions($target);

        $result = $this->assembler->assemble('\ExampleClass');

        $this->assertSame($target, $result[0]);
        $this->assertSame($default, $result[1][0]);
    }

    /**
     * @test
     * @covers ::assemble
     */
    public function Assemble_ParameterWithDefaultValueUserArgumentMatchingName_PassedUserValue()
    {
        $param1 = new UnsanitizedParameter('someName', null, 'default');
        $target = new UnsanitizedClassDefinition('\ExampleClass', array($param1));

        $this->addDefinitions($target);

        $result = $this->assembler->assemble('\ExampleClass', array('someName' => 'user'));

        $this->assertSame($target, $result[0]);
        $this->assertSame('user', $result[1][0]);
    }

    /**
     * @test
     * @covers ::assemble
     */
    public function Assemble_ParameterWithDefaultValueUserArgumentNotMatchingName_PassedDefaultValue()
    {
        $param1 = new UnsanitizedParameter('someName', null, 'default');
        $target = new UnsanitizedClassDefinition('\ExampleClass', array($param1));

        $this->addDefinitions($target);

        $result = $this->assembler->assemble('\ExampleClass', array('otherName' => 'user'));

        $this->assertSame($target, $result[0]);
        $this->assertSame('default', $result[1][0]);
    }

    /**
     * @test
     * @covers ::assemble
     */
    public function Assemble_ParameterWithoutDefaultValueUserArgumentMatchingName_PassedUserValue()
    {
        $param1 = new UnsanitizedParameter('someName', null);
        $target = new UnsanitizedClassDefinition('\ExampleClass', array($param1));

        $this->addDefinitions($target);

        $result = $this->assembler->assemble('\ExampleClass', array('someName' => 'user'));

        $this->assertSame($target, $result[0]);
        $this->assertSame('user', $result[1][0]);
    }

    /**
     * @test
     * @covers ::assemble
     * @expectedException \Kampaw\Dic\Exception\BadMethodCallException
     */
    public function Assemble_ParameterWithoutDefaultValueNoUserArguments_ThrowsException()
    {
        $param1 = new UnsanitizedParameter('someName', null);
        $target = new UnsanitizedClassDefinition('\ExampleClass', array($param1));

        $this->addDefinitions($target);

        $this->assembler->assemble('\ExampleClass');
    }

    /**
     * @test
     * @covers ::assemble
     */
    public function Assemble_ParameterWithDefaultValueUserArgumentWithoutName_PassedUserValue()
    {
        $param1 = new UnsanitizedParameter('someName', null, 'default');
        $target = new UnsanitizedClassDefinition('\ExampleClass', array($param1));

        $this->addDefinitions($target);

        $result = $this->assembler->assemble('\ExampleClass', array('user'));

        $this->assertSame($target, $result[0]);
        $this->assertSame('user', $result[1][0]);
    }

    /**
     * @test
     * @covers ::assemble
     */
    public function Assemble_ParameterWithoutDefaultValueUserArgumentWithoutName_PassedUserValue()
    {
        $param1 = new UnsanitizedParameter('someName', null);
        $target = new UnsanitizedClassDefinition('\ExampleClass', array($param1));

        $this->addDefinitions($target);

        $result = $this->assembler->assemble('\ExampleClass', array('user'));

        $this->assertSame($target, $result[0]);
        $this->assertSame('user', $result[1][0]);
    }

    /**
     * @test
     * @covers ::assemble
     */
    public function Assemble_TwoParametersWithDefaultValueNoUserArguments_PassedDefaultValues()
    {
        $param1 = new UnsanitizedParameter('param1', null, 'default1');
        $param2 = new UnsanitizedParameter('param2', null, 'default2');
        $target = new UnsanitizedClassDefinition('\ExampleClass', array($param1, $param2));

        $this->addDefinitions($target);

        $result = $this->assembler->assemble('\ExampleClass');

        $this->assertSame($target, $result[0]);
        $this->assertSame('default1', $result[1][0]);
        $this->assertSame('default2', $result[1][1]);
    }

    /**
     * @test
     * @covers ::assemble
     */
    public function Assemble_TwoParametersWithoutDefaultValueTwoUserArguments_PassedUserValues()
    {
        $param1 = new UnsanitizedParameter('param1', null);
        $param2 = new UnsanitizedParameter('param2', null);
        $target = new UnsanitizedClassDefinition('\ExampleClass', array($param1, $param2));

        $this->addDefinitions($target);

        $result = $this->assembler->assemble('\ExampleClass', array('user1', 'user2'));

        $this->assertSame($target, $result[0]);
        $this->assertSame('user1', $result[1][0]);
        $this->assertSame('user2', $result[1][1]);
    }

    /**
     * @test
     * @covers ::assemble
     */
    public function Assemble_TwoParametersWithDefaultValueTwoUserArguments_PassedUserValues()
    {
        $param1 = new UnsanitizedParameter('param1', null, 'default1');
        $param2 = new UnsanitizedParameter('param2', null, 'default2');
        $target = new UnsanitizedClassDefinition('\ExampleClass', array($param1, $param2));

        $this->addDefinitions($target);

        $result = $this->assembler->assemble('\ExampleClass', array('user1', 'user2'));

        $this->assertSame($target, $result[0]);
        $this->assertSame('user1', $result[1][0]);
        $this->assertSame('user2', $result[1][1]);
    }

    /**
     * @test
     * @covers ::assemble
     * @expectedException \Kampaw\Dic\Exception\BadMethodCallException
     */
    public function Assemble_TwoParametersFirstDefaultSecondMissingValue_ThrowsException()
    {
        $param1 = new UnsanitizedParameter('param1', null, 'default1');
        $param2 = new UnsanitizedParameter('param2', null);
        $target = new UnsanitizedClassDefinition('\ExampleClass', array($param1, $param2));

        $this->addDefinitions($target);

        $this->assembler->assemble('\ExampleClass');
    }

    /**
     * @test
     * @covers ::assemble
     * @expectedException \Kampaw\Dic\Exception\BadMethodCallException
     */
    public function Assemble_TwoParametersFirstUserSecondMissingValue_ThrowsException()
    {
        $param1 = new UnsanitizedParameter('param1', null);
        $param2 = new UnsanitizedParameter('param2', null);
        $target = new UnsanitizedClassDefinition('\ExampleClass', array($param1, $param2));

        $this->addDefinitions($target);

        $this->assembler->assemble('\ExampleClass', array());
    }

    /**
     * @test
     * @covers ::assemble
     * @expectedException \Kampaw\Dic\Exception\BadMethodCallException
     */
    public function Assemble_TwoParametersFirstDefaultSecondNotMatchingName_ThrowsException()
    {
        $param1 = new UnsanitizedParameter('param1', null, 'default1');
        $param2 = new UnsanitizedParameter('param2', null);
        $target = new UnsanitizedClassDefinition('\ExampleClass', array($param1, $param2));

        $this->addDefinitions($target);

        $this->assembler->assemble('\ExampleClass', array('third' => 'param2'));
    }

    /**
     * @test
     * @covers ::assemble
     */
    public function Assemble_TwoParametersFirstUserWithoutNameSecondDefault_PassedUserAndDefault()
    {
        $param1 = new UnsanitizedParameter('param1', null);
        $param2 = new UnsanitizedParameter('param2', null, 'default2');
        $target = new UnsanitizedClassDefinition('\ExampleClass', array($param1, $param2));

        $this->addDefinitions($target);

        $result = $this->assembler->assemble('\ExampleClass', array('user1'));

        $this->assertSame($target, $result[0]);
        $this->assertSame('user1', $result[1][0]);
        $this->assertSame('default2', $result[1][1]);
    }

    /**
     * @test
     * @covers ::assemble
     */
    public function Assemble_TwoParametersFirstUserMatchingNameSecondUserWithoutName_PassedUserValues()
    {
        $param1 = new UnsanitizedParameter('param1', null);
        $param2 = new UnsanitizedParameter('param2', null);
        $target = new UnsanitizedClassDefinition('\ExampleClass', array($param1, $param2));

        $this->addDefinitions($target);

        $result = $this->assembler->assemble('\ExampleClass', array('param1' => 'user1', 'user2'));

        $this->assertSame($target, $result[0]);
        $this->assertSame('user1', $result[1][0]);
        $this->assertSame('user2', $result[1][1]);
    }

    /**
     * @test
     * @covers ::assemble
     */
    public function Assemble_ParameterWithClassHintUserArgumentMatchingName_PassedUserValue()
    {
        $param1 = new UnsanitizedParameter('someName', '\ArrayObject');
        $target = new UnsanitizedClassDefinition('\ExampleClass', array($param1));

        $this->addDefinitions($target);

        $argument = new \ArrayObject();
        $result = $this->assembler->assemble('\ExampleClass', array('someName' => $argument));

        $this->assertSame($target, $result[0]);
        $this->assertSame($argument, $result[1][0]);
    }

    /**
     * @test
     * @covers ::assemble
     */
    public function Assemble_ParameterWithClassHintUserArgumentWithoutName_PassedUserValue()
    {
        $param1 = new UnsanitizedParameter('someName', '\ArrayObject');
        $target = new UnsanitizedClassDefinition('\ExampleClass', array($param1));

        $this->addDefinitions($target);

        $argument = new \ArrayObject();
        $result = $this->assembler->assemble('\ExampleClass', array($argument));

        $this->assertSame($target, $result[0]);
        $this->assertSame($argument, $result[1][0]);
    }

    /**
     * @test
     * @covers ::assemble
     * @expectedException \Kampaw\Dic\Exception\InvalidArgumentException
     */
    public function Assemble_ParameterWithClassHintUserArgumentWrongClass_ThrowsException()
    {
        $param1 = new UnsanitizedParameter('someName', '\ArrayObject');
        $target = new UnsanitizedClassDefinition('\ExampleClass', array($param1));

        $this->addDefinitions($target);

        $argument = new \stdClass();
        $this->assembler->assemble('\ExampleClass', array($argument));
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
     * @covers ::assemble
     * @dataProvider nonArrayValueProvider
     * @expectedException \Kampaw\Dic\Exception\InvalidArgumentException
     */
    public function Assemble_ParameterWithArrayHintUserArgumentNotArray_ThrowsException($argument)
    {
        $param1 = new UnsanitizedParameter('arrayHint', 'array');
        $target = new UnsanitizedClassDefinition('\ExampleClass', array($param1));

        $this->addDefinitions($target);

        $this->assembler->assemble('\ExampleClass', array($argument));
    }

    /**
     * @test
     * @covers ::assemble
     * @expectedException \Kampaw\Dic\Exception\LogicException
     */
    public function Assemble_ConcreteDependencyDefinitionMissing_ThrowsException()
    {
        $param1 = new UnsanitizedParameter('concrete', '\ClassDependency');
        $target = new UnsanitizedClassDefinition('\ExampleClass', array($param1));

        $this->addDefinitions($target);

        $this->assembler->assemble('\ExampleClass');
    }

    /**
     * @test
     * @covers ::assemble
     */
    public function Assemble_ConcreteDependencyDefinitionInRepository_UsedRepositoryDefinition()
    {
        $param1 = new UnsanitizedParameter('concrete', '\ClassDependency');
        $target = new UnsanitizedClassDefinition('\ExampleClass', array($param1));
        $dependency = new UnsanitizedClassDefinition('\ClassDependency');

        $this->addDefinitions($target, $dependency);

        $result = $this->assembler->assemble($target->getClass());

        $this->assertSame($target, $result[0]);
        $this->assertSame($dependency, $result[1][0][0]);
    }

    /**
     * @test
     * @covers ::assemble
     */
    public function Assemble_NestedConcreteDependencyDefinitionsInRepository_UsedRepositoryDefinitions()
    {
        $level2 = new UnsanitizedClassDefinition('\Level_2_Dependency');

        $param1 = new UnsanitizedParameter('level_2_dependency', '\Level_2_Dependency');
        $level1 = new UnsanitizedClassDefinition('\Level_1_Dependency', array($param1));

        $param2 = new UnsanitizedParameter('level_1_dependency', '\Level_1_Dependency');
        $target = new UnsanitizedClassDefinition('\ExampleClass', array($param2));

        $this->addDefinitions($target, $level1, $level2);

        $result = $this->assembler->assemble($target->getClass());

        $this->assertSame($target, $result[0]);
        $this->assertSame($level1, $result[1][0][0]);
        $this->assertSame($level2, $result[1][0][1][0][0]);
    }

    /**
     * @test
     * @covers ::assemble
     * @expectedException \Kampaw\Dic\Exception\CircularDependencyException
     */
    public function Assemble_CircularDependency_ThrowsException()
    {
        $param1 = new UnsanitizedParameter('level_1_dependency', '\CircularDependency');
        $target = new UnsanitizedClassDefinition('\CircularDependency', array($param1));

        $this->addDefinitions($target);

        $this->assembler->assemble($target->getClass());
    }

    /**
     * @test
     * @covers ::assemble
     */
    public function Assemble_ParallelConcreteDependency_UsedRepositoryDefinition()
    {
        $parallel = new UnsanitizedClassDefinition('\ParallelDependency');

        $param1 = new UnsanitizedParameter('param1', '\ParallelDependency');
        $param2 = new UnsanitizedParameter('param2', '\ParallelDependency');
        $target = new UnsanitizedClassDefinition('\ExampleClass', array($param1, $param2));

        $this->addDefinitions($target, $parallel);

        $result = $this->assembler->assemble($target->getClass());

        $this->assertSame($target, $result[0]);
        $this->assertSame($parallel, $result[1][0][0]);
        $this->assertSame($parallel, $result[1][1][0]);
    }
}