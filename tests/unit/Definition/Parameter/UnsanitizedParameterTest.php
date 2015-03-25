<?php

namespace Kampaw\Dic\Definition\Parameter;

/**
 * @coversDefaultClass \Kampaw\Dic\Definition\Parameter\UnsanitizedParameter
 * @covers ::<!public>
 */
class UnsanitizedParameterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers ::__construct
     * @covers ::getName
     */
    public function Construct_ExampleName_NameIsSet()
    {
        $parameter = new UnsanitizedParameter('exampleName');

        $this->assertSame('exampleName', $parameter->getName());
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::getType
     */
    public function Construct_ExampleType_TypesSet()
    {
        $parameter = new UnsanitizedParameter('exampleName', 'exampleType');

        $this->assertSame('exampleType', $parameter->getType());
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::getValue
     */
    public function Construct_ExampleValue_ValuesSet()
    {
        $parameter = new UnsanitizedParameter('exampleName', 'exampleType', 'exampleValue');

        $this->assertSame('exampleValue', $parameter->getValue());
    }
}
