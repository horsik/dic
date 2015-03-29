<?php

namespace Kampaw\Dic\Definition\InterfaceDefinition;

use Kampaw\Dic\Definition\ClassDefinition\UnsanitizedClassDefinition;

/**
 * @coversDefaultClass Kampaw\Dic\Definition\InterfaceDefinition\AbstractInterfaceDefinition
 * @covers ::__construct
 * @covers ::<!public>
 * @uses \Kampaw\Dic\Definition\ClassDefinition\AbstractClassDefinition
 * @uses \Kampaw\Dic\Definition\ClassDefinition\UnsanitizedClassDefinition
 */
class AbstractInterfaceDefinitionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractInterfaceDefinition $definition
     */
    private $definition;

    public function setUp()
    {
        $this->definition = $this->getMockBuilder(__NAMESPACE__ . '\AbstractInterfaceDefinition')
             ->disableOriginalConstructor()
             ->setMethods(array('getCandidates'))
             ->getMockForAbstractClass();
    }

    /**
     * @test
     */
    public function Construct_ExampleInterface_PassedToSetter()
    {
        $this->definition
              ->expects($this->once())
              ->method('setInterface')
              ->with('exampleInterface');

        $this->definition->__construct('exampleInterface', array());
    }

    /**
     * @test
     */
    public function Construct_ExampleCandidate_PassedToSetter()
    {
        $this->definition
             ->expects($this->once())
             ->method('addCandidate')
             ->with('exampleCandidate');

        $this->definition->__construct('exampleInterface', array('exampleCandidate'));
    }

    /**
     * @test
     * @covers ::getCandidate
     */
    public function GetCandidate_CandidateNotSet_ReturnsFirst()
    {
        $candidates = array('first', 'second', 'third');

        $this->definition->method('getCandidates')->willReturn($candidates);

        $this->assertSame('first', $this->definition->getCandidate());
    }

    /**
     * @test
     * @covers ::setCandidateByIndex
     * @covers ::getCandidate
     */
    public function SetCandidateByIndexValidIndex_CandidateIsSet()
    {
        $candidates = array('first', 'second', 'third');

        $this->definition->method('getCandidates')->willReturn($candidates);
        $this->definition->setCandidateByIndex(1);

        $this->assertSame('second', $this->definition->getCandidate());
    }

    /**
     * @test
     * @covers ::setCandidateByIndex
     * @expectedException \Kampaw\Dic\Exception\OutOfBoundsException
     */
    public function SetCandidateByIndexIndexNegative_ThrowsException()
    {
        $candidates = array('first', 'second', 'third');

        $this->definition->method('getCandidates')->willReturn($candidates);
        $this->definition->setCandidateByIndex(-1);
    }

    /**
     * @test
     * @covers ::setCandidateByIndex
     * @expectedException \Kampaw\Dic\Exception\OutOfBoundsException
     */
    public function SetCandidateByIndexIndexPositiveOutOfBounds_ThrowsException()
    {
        $candidates = array('first', 'second', 'third');

        $this->definition->method('getCandidates')->willReturn($candidates);
        $this->definition->setCandidateByIndex(3);
    }

    public function nonIntegerTypeProvider()
    {
        return array(
            array(false),
            array(null),
            array(''),
            array('0'),
            array('invalid'),
            array(array()),
            array(new \stdClass()),
            array(function() {}),
            array(NAN),
        );
    }

    /**
     * @test
     * @covers ::setCandidateByIndex
     * @dataProvider nonIntegerTypeProvider
     * @expectedException \Kampaw\Dic\Exception\InvalidArgumentException
     */
    public function SetCandidateByIndexInvalidCandidateType_ThrowsException($type)
    {
        $this->definition->setCandidateByIndex($type);
    }

    /**
     * @test
     * @covers ::setCandidateByDefinition
     * @covers ::getCandidate
     */
    public function SetCandidateByDefinition_ValidCandidate_CandidateIsSet()
    {
        $candidates = array(
            new UnsanitizedClassDefinition('first'),
            new UnsanitizedClassDefinition('second'),
        );

        $this->definition->method('getCandidates')->willReturn($candidates);
        $this->definition->setCandidateByDefinition($candidates[1]);

        $this->assertSame($candidates[1], $this->definition->getCandidate());
    }

    /**
     * @test
     * @covers ::setCandidateByDefinition
     * @expectedException \Kampaw\Dic\Exception\DomainException
     */
    public function SetCandidateByDefinition_InvalidCandidate_ThrowsException()
    {
        $candidates = array(
            new UnsanitizedClassDefinition('first'),
        );

        $this->definition->method('getCandidates')->willReturn($candidates);
        $this->definition->setCandidateByDefinition(new UnsanitizedClassDefinition('second'));
    }

    /**
     * @test
     * @covers ::getClass
     * @covers ::getCandidate
     */
    public function GetClass_DefinitionsSet_ReturnsClass()
    {
        $candidates = array(
            new UnsanitizedClassDefinition('first'),
        );

        $this->definition->method('getCandidates')->willReturn($candidates);

        $this->assertSame('first', $this->definition->getClass());
    }

    /**
     * @test
     * @covers ::getParameters
     * @covers ::getCandidate
     */
    public function GetParameters_DefinitionsSet_ReturnsParameters()
    {
        $candidates = array(
            new UnsanitizedClassDefinition('first', array('parameters')),
        );

        $this->definition->method('getCandidates')->willReturn($candidates);

        $this->assertSame(array('parameters'), $this->definition->getParameters());
    }
}