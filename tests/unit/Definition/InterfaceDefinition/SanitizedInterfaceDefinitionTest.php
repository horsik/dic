<?php

namespace Kampaw\Dic\Definition\InterfaceDefinition;
use Kampaw\Dic\Definition\ClassDefinition\SanitizedClassDefinition;
use Kampaw\Dic\Definition\ClassDefinition\UnsanitizedClassDefinition;

/**
 * @coversDefaultClass \Kampaw\Dic\Definition\InterfaceDefinition\SanitizedInterfaceDefinition
 * @covers ::__construct
 * @covers ::<!public>
 * @uses \Kampaw\Dic\Definition\InterfaceDefinition\AbstractInterfaceDefinition
 * @uses \Kampaw\Dic\Definition\ClassDefinition\AbstractClassDefinition
 * @uses \Kampaw\Dic\Definition\ClassDefinition\UnsanitizedClassDefinition
 * @uses \Kampaw\Dic\Definition\ClassDefinition\SanitizedClassDefinition
 */
class SanitizedInterfaceDefinitionTest extends \PHPUnit_Framework_TestCase
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
    public function Construct_InvalidInterfaceType_ThrowsException($interface)
    {
        new SanitizedInterfaceDefinition($interface, array());
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\DomainException
     */
    public function Construct_NonExistentInterface_ThrowsException()
    {
        new SanitizedInterfaceDefinition('nonExistent', array());
    }

    /**
     * @test
     * @covers ::getInterface
     */
    public function Construct_ValidInterface_InterfaceIsSet()
    {
        $candidate = new SanitizedClassDefinition('\ArrayObject');
        $definition = new SanitizedInterfaceDefinition('\ArrayAccess', array($candidate));

        $this->assertSame('\ArrayAccess', $definition->getInterface());
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\InvalidArgumentException
     */
    public function Construct_CandidateNotImplementsAnInterface_ThrowsException()
    {
        $candidate = new SanitizedClassDefinition('\stdClass');

        new SanitizedInterfaceDefinition('\ArrayAccess', array($candidate));
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\BadMethodCallException
     */
    public function Construct_EmptyCandidates_ThrowException()
    {
        new SanitizedInterfaceDefinition('\ArrayAccess', array());
    }

    /**
     * @test
     */
    public function Construct_ValidSanitizedCandidate_CandidateIsSet()
    {
        $candidate = new SanitizedClassDefinition('\DateTime');
        $definition = new SanitizedInterfaceDefinition('\DateTimeInterface', array($candidate));

        $result = $definition->getCandidate();

        $this->assertSame('\DateTime', $result->getClass());
    }

    /**
     * @test
     */
    public function Construct_ValidUnsanitizedCandidate_CandidateIsSanitizedAndSet()
    {
        $candidate = new UnsanitizedClassDefinition('\DateTime');
        $definition = new SanitizedInterfaceDefinition('\DateTimeInterface', array($candidate));

        $result = $definition->getCandidate();

        $this->assertInstanceOf('\Kampaw\Dic\Definition\ClassDefinition\SanitizedClassDefinition', $result);
        $this->assertSame('\DateTime', $result->getClass());
    }
}